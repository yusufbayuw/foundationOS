# ADR 0001 — Tenancy Package Evaluation

**Date:** 2026-05-22
**Status:** Accepted
**Deciders:** Engineering team

---

## Context

FoundationOS uses shared-database multi-tenancy: one database, every operational table carries `tenant_id`. After completing Fase 4.1–4.3, the in-house tenancy stack is fully operational. This ADR evaluates whether adopting `stancl/tenancy` or `spatie/laravel-multitenancy` would improve the system.

---

## Current In-House Architecture (Fase 4.1–4.3)

| Component | File | Role |
|---|---|---|
| `BelongsToTenant` trait | `Modules/Core/app/Models/Concerns/BelongsToTenant.php` | Boots `TenantScope` global scope on every model |
| `TenantScope` | `app/Scopes/TenantScope.php` | Filters all queries by `CurrentTenant::id()` |
| `CurrentTenant` singleton | `app/Support/CurrentTenant.php` | Thread-local tenant context with Filament, manual-set, and `forTenant()` modes |
| `ResolveApiTenant` middleware | `app/Http/Middleware/ResolveApiTenant.php` | Resolves tenant from Sanctum token for API requests |
| `BindTenantToContainer` middleware | `app/Http/Middleware/BindTenantToContainer.php` | Sets `CurrentTenant` for web requests |
| `WithTenantContext` queue middleware | `app/Queue/Middleware/WithTenantContext.php` | Restores tenant context inside queued jobs |
| `TenantRunCommand` | `app/Console/Commands/TenantRunCommand.php` | Artisan `tenant:run` for running commands in tenant scope |

**Acceptance tests passing:**
- `TenantScopeIsolationTest` — verifies tenant A data is invisible to tenant B
- `JobRestoresTenantContextTest` — verifies queued jobs restore tenant context
- `TenancySwitchAuditTest` — verifies audit trail for tenant switches

---

## Options Evaluated

### Option A — Keep In-House (Recommended)

**Pros:**
- Already fully implemented and tested.
- No external package dependency to maintain or keep updated.
- Tailored to FoundationOS: custom `assignee_config`, `organization_id` scoping, Filament panel tenant awareness, and Sanctum token-scoped API tenancy are all handled natively.
- Zero migration risk: existing migrations, models, and tests stay unchanged.
- Full control over `CurrentTenant` lifecycle — critical for the `WorkflowCanvas` Livewire component and API middleware.
- `BelongsToTenant::withoutTenantScope()` escape hatch is already implemented and used in `StudentInvoice`, `WebhookSubscription`, etc.

**Cons:**
- Must maintain custom code if requirements grow (e.g., tenant-specific DB connections, subdomain routing).
- No community package maintenance.

### Option B — `stancl/tenancy` (single-database mode)

**Pros:**
- Community maintained, well-documented.
- Single-database mode matches our architecture.
- Built-in subdomain + domain routing if ever needed.

**Cons:**
- Major migration: `stancl/tenancy` global scopes conflict with our `BelongsToTenant` trait. Every model would need re-wiring.
- Package overrides `Illuminate\Database\Eloquent\Builder` in ways that conflict with Filament v5's query customizations.
- Single-database mode in `stancl/tenancy` still assumes tenant identification via domain/subdomain by default; our token-based API tenant resolution would need custom overrides.
- `InitializeTenancyByRequestData` middleware and our `ResolveApiTenant` would both try to initialize tenancy — requires careful ordering.
- Package upgrades are external dependency; breaking changes have occurred across minor versions.
- No demonstrated use case requiring a feature `stancl/tenancy` provides that the in-house stack cannot.

### Option C — `spatie/laravel-multitenancy`

**Pros:**
- More lightweight than `stancl/tenancy`, hooks into Eloquent global scopes similarly to our approach.

**Cons:**
- Same model migration cost as Option B.
- Less feature-rich than our current stack for API-first tenancy (no Sanctum integration).
- The `HasTenant` trait conflicts with our `BelongsToTenant` unless all models are migrated simultaneously.
- Spatie's approach does not support optional `organization_id` sub-scoping out of the box.

---

## Decision

**Continue with Option A (in-house tenancy stack).**

The in-house implementation satisfies all current requirements:

| Requirement | In-House | stancl | spatie |
|---|---|---|---|
| Shared-DB with `tenant_id` | ✅ | ✅ | ✅ |
| Global scope on all models | ✅ | ✅ | ✅ |
| Filament panel tenant-aware | ✅ | ⚠️ custom | ⚠️ custom |
| Sanctum API token scoping | ✅ | ❌ custom | ❌ custom |
| Queue job context restore | ✅ | ✅ | ⚠️ manual |
| `organization_id` sub-scoping | ✅ | ❌ | ❌ |
| `withoutTenantScope()` escape | ✅ | ✅ | ✅ |
| Tenant-specific DB connections | ❌* | ✅ | ✅ |

*Tenant-specific DB connections are not a current requirement. If this becomes a requirement, revisit this ADR.

---

## Consequences

- No package adoption at this time.
- `Fase 4.4` is closed as **not adopted** (in-house solution is sufficient).
- Revisit if a concrete use case arises that the in-house stack cannot satisfy (most likely: tenant-specific database connections, or subdomain-based tenant routing at scale).
- The in-house stack should be considered the authoritative approach for all future modules.

---

## References

- [stancl/tenancy docs](https://tenancyforlaravel.com/docs/v3/single-database-tenancy/)
- [spatie/laravel-multitenancy docs](https://spatie.be/docs/laravel-multitenancy/v3/introduction)
- `app/Support/CurrentTenant.php` — in-house singleton
- `Modules/Core/app/Models/Concerns/BelongsToTenant.php` — in-house model trait
- `tests/Feature/TenantScopeIsolationTest.php` — isolation guarantees
