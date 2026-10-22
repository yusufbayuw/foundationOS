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
