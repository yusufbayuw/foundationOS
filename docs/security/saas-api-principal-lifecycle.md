# FoundationOS tenant API principal and subscription policy

Scope: issue #76; APIs /api/v1 and /api/v2; PR #77 established the initial subscription guard.

## Tenant status

| Status | API policy |
| --- | --- |
| active | Allowed when subscription_expires_at is null or in the future |
| trial | Allowed when trial_ends_at is null or in the future |
| past_due | Allowed only strictly before grace_period_ends_at |
| suspended, unknown, deleted or missing | Denied |

Denials return HTTP 403 with an error.code suitable for clients. Public payment/billing webhooks stay outside the authenticated tenant groups so billing can be recovered. The existing Filament billing recovery page is unchanged. Null expiration fields remain valid for pre-existing manual and perpetual subscriptions.

## Human API tokens

- Authenticated tenant identity is always the server-side personal_access_tokens.tenant_id; tenant_id supplied in a request cannot switch the context.
- A human token holder must have an active (not deleted, not expired) user_tenant_roles assignment in the same tenant on every API request. Revocation or assignment expiry takes effect on the following request.
- Global super admins need the same membership unless their personal access token explicitly lists the platform:tenants ability. A wildcard '*' is not an explicit platform grant.
- TENANCY_API_ENFORCE_MEMBERSHIP defaults to true in production, false in nonproduction for backwards compatibility with older test fixtures. Set true in staging for rollout verification and leave true in production. Setting it false in production weakens security and is not a supported steady state.
- Sanctum's expired/deleted tokens are rejected prior to the tenant middleware. Existing human token issuance, including passkeys, remains owned by the Security Agent. Existing null expires_at user tokens continue to follow Sanctum's legacy configuration; coordinate explicit expiry and token rotation with Security.
- In-memory Sanctum::actingAs users remain supported only in legacy unit tests. Real HTTP API traffic requires a persisted token, not a user cookie with implicit tenant access.

## Machine-to-machine principal

App\Models\TenantServiceIntegration is an independent Sanctum authenticatable, not a user with borrowed membership or a superadmin bypass. The additive tenant_service_integrations table stores tenant_id, status, expires_at and revoked_at. The tenant subscription, service status/expiry, token tenant and token expiry are validated on every request.

The machine allowlist is intentionally limited to GET /api/v1/organizations, GET /api/v1/organizations/{organization}, and the matching v2 routes. Only tokens with the explicit api:read ability and non-null token expiry are accepted. No user profile, enrollment, employee or write API is exposed to machine principals by default. Each expansion needs a separate endpoint-specific access and privacy review.

Operator-only provisioning example in a trusted console (never log or paste plaintext tokens into issues):

```php
$integration = \App\Models\TenantServiceIntegration::create([
    'tenant_id' => $tenant->id,
    'name' => 'Directory integration',
]);
$issued = $integration->issueReadToken('organization-directory');
// Deliver $issued->plainTextToken exactly once via a secure channel.
```

issueReadToken() creates a 30-day token atomically and pins tenant_id in the token row. To revoke an entire integration, set revoked_at to now() or status to disabled. To revoke only one credential, delete its personal_access_tokens row. Rotate by issuing a replacement then revoking the old one. Do not use an integration principal as a regular login account.

## Deployment and verification

1. Back up production data and inventory existing persisted user tokens whose owners have no active membership. These will be denied in production; review them individually and provision genuine memberships or rotate to service identities. Never automatically grant memberships to match existing tokens.
2. Run php artisan migrate --force to create only tenant_service_integrations. No existing table is modified or deleted.
3. Test with TENANCY_API_ENFORCE_MEMBERSHIP=true in staging; run php artisan test --filter=ApiTenantPrincipalAccessTest and the existing tests/Tenant, API, and checkout tests; confirm webhooks still work.
4. Deploy and watch HTTP 403 tenant_membership_required, tenant_subscription_inactive and service_integration_denied metrics. Never log bearer tokens.

## Remaining broader-scope audits

CurrentTenant restores the request context with try/finally; Spatie team IDs are likewise restored. Console/queue processes still need explicit tenant context for each job. Model scopes do not by themselves protect raw SQL, unscoped relations, cache keys, exports or file URLs. These require separate targeted module reviews, negative tests and decisions coordinated by the Project Manager.

## Hardening and rollout addendum (2026-10-22, issue #85 / #98 / #97)

- API access checks fresh, non-deleted human user status == `active` on every persisted bearer-token request, regardless of membership mode. Changes to user status deny immediately, including platform owners. A cancelled/expired tenant subscription blocks API access regardless of token scope.
- Filament tenant middleware now uses `CurrentTenant::forTenant()` and restores the previous Spatie permissions team ID on both normal and exceptional completion; null context is explicitly isolated to avoid stale singleton contamination.
- Tenant-scoped Eloquent model `save()` and `create()` reject explicit foreign `tenant_id` when a CurrentTenant is bound, including attempts to repurpose models originally belonging to a different tenant. Raw DB queries and builder bulk update/delete do not invoke Eloquent lifecycle hooks; those are **not** covered by this mitigation and must be reviewed separately.
- Jobs adopting `InteractsWithTenant` now fail if no `tenantId` was captured. Explicit tenant-bound queue middleware loads an existing, non-deleted tenant before running and restores tenant and Spatie-team context after execution. Suspended tenants are **not** blocked in this queue middleware: billing recovery, cleanup, and asynchronous reconciliation may still require jobs. Any globally authorized job should not use `InteractsWithTenant`; the PM and DevOps team must review such jobs.
- New self-registrations begin a finite trial (default 14 days; `TENANCY_SELF_REGISTRATION_TRIAL_DAYS` configures length). Existing tenant status, plan, trial dates, expiry dates and subscriptions are not changed. Trial expiry is enforced by authenticated v1/v2 tenant API middleware. The corresponding Filament billing/activation UX and paid plan entitlement transitions are a separate business-policy integration gate.
- Subscription module activation requires a plan/add-on entitlement model that accounts for paid-per-module products. No blanket check on `SubscriptionPlan.included_modules` is introduced yet because that alone would disable legitimately paid add-ons. Track with #97 and the Finance agent.

### Manual UAT scenarios before merging

1. A valid user belonging to tenant A uses an A-bound bearer token successfully; remove/expire their role, observe `tenant_membership_required` on both API versions. Try a B tenant_id query/header; B data must remain unavailable.
2. Disable the user while retaining token and assignment; observe `tenant_user_inactive` on both API versions, including global admin tokens.
3. Suspend the tenant or expire its trial; observe `tenant_subscription_inactive`. Confirm signed payment webhook and existing billing recovery workflows can still operate.
4. Sequentially open tenant A and B Filament contexts with the same worker, then cause a controlled exception. The previous CurrentTenant and permission team ID must be restored; no cache bleed may occur.
5. Attempt create/update of a tenant B model inside A CurrentTenant; expect `AuthorizationException` without any persistence. Repeat from a job with no tenant context: a job using `InteractsWithTenant` must be denied before executing.
6. Verify that tenant signup assigns an owner and module profile transactionally, and the new record starts in finite trial. Existing tenants must retain prior entitlements.
7. Check the exact user-token inventory and signed webhook recovery in staging, including 403 monitoring, before approving production enforcement.

### Explicit residual risks

Raw query writes, bulk builder writes, file path authorization, CDN/private uploads, queued jobs that do not adopt InteractsWithTenant, Filament/Livewire direct model access, cache key naming, invoice reactivation, and optional per-module/add-on enforcement are not fully proven safe by these focused tests. These require separate module-owner negative tests, an asset inventory, and isolated PRs. Do not interpret green CI as an exhaustive tenant isolation guarantee.

## CI regression correction (2026-10-23)

The strict NULL-`tenantId` queue rejection introduced in this PR broke existing framework and explicitly global jobs (`ProcessMoodleSyncOutboxJob`, `CheckWorkflowSlaJob`, `CallQueuedListener`). The middleware now permits only those three reviewed-by-name legacy job types to execute with NULL tenant context. Crucially, execution is wrapped in `CurrentTenant::forTenant(null)`, the Spatie permission team is reset to `0` for the callback, and previous context/team are restored in `finally`; unrelated jobs still fail closed. **This is an operational compatibility exception, not proof that all queued listeners are fully tenant-safe.** Audit individual listener targets and target-record authorization before tightening that temporary allowance.

The cross-tenant Eloquent `saving` guard remains strict. Existing integration tests that intentionally construct foreign-tenant fixtures now explicitly enter the foreign tenant context while creating those records, avoiding fake security exceptions during test setup. The subsequent cross-tenant API negative assertions remain unchanged.

Laravel static analysis now has an explicit typed Eloquent `Tenant::$trial_ends_at` property annotation. No migration or persisted tenant data was changed by this corrective patch.
