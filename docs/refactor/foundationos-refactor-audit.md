# foundationOS Refactor Audit: Module Boundaries

Date: 2026-05-24

## 1. Current Architecture Summary

foundationOS is a Laravel 13 modular ERP/SaaS application using PHP 8.4 locally, Filament 5, Livewire 4, Sanctum, Filament Shield, coolsam/modules, Midtrans, and Spatie Activitylog. The root `composer.json` registers each business module through PSR-4 autoloading under `Modules\...`, and `coolsam/modules` discovers enabled modules at runtime.

The Filament surface is split into two panels:

- `AdminPanelProvider` is the tenant-aware operational panel at `/admin`. It registers `ModulesPlugin`, Shield, tenant registration, tenant branding, module navigation groups, tenant middleware, and dynamically discovers resources/pages/widgets from every enabled module.
- `PlatformPanelProvider` is the global platform-owner panel at `/platform`. It discovers only `App\Filament\Platform` classes and does not configure Filament tenancy.

Tenant context is centralized around:

- `Modules\Core\Models\Concerns\BelongsToTenant`, which adds `TenantScope`, creates a `tenant()` relationship, and auto-fills `tenant_id` on create.
- `App\Scopes\TenantScope`, which scopes tenant-aware models only when `CurrentTenant` is bound and has an id.
- `App\Support\CurrentTenant`, which resolves a manually set tenant first, then falls back to `Filament::getTenant()`.
- `App\Http\Middleware\ResolveApiTenant`, which sets `CurrentTenant` from the authenticated Sanctum personal access token's `tenant_id`.

The API v1 surface is intentionally small and mostly read-oriented. Routes under `/api/v1` are throttled, authenticated with Sanctum, and tenant-bound through `resolve.api.tenant`. Write routes are additionally wrapped by `idempotency`.

Billing is currently handled by `App\Services\BillingService`. It calculates subscription amounts, generates subscription invoices, creates Midtrans Snap payments, and handles webhook notifications by updating `SubscriptionLog` and activating the tenant subscription.

## 2. Module Dependency Problems

The main boundary issue is that Core owns foundational identity and tenancy models, but `Tenant` and `User` directly import feature-module models from School, Finance, Procurement, Library, Employee, Campus, Enrollment, and Monitoring.

This creates an inverted dependency:

- Feature modules correctly depend on Core for tenant/user primitives.
- Core also depends back on feature modules for convenience relationships.
- Disabling, renaming, extracting, or independently testing a feature module can break Core model autoloading.
- Core models become the default place to add every new module relationship, which steadily increases coupling and merge conflict risk.

The current `AdminPanelProvider` also centralizes module discovery and navigation grouping in the app provider. That is acceptable for now, but it means panel boot behavior is sensitive to every enabled module and should be changed carefully.

`ModuleResource` is another central coupling point. It is used as the base resource class across modules, but it contains UI labeling, icon heuristics, module activation checks, tenant-scope detection, soft-delete query behavior, global mutation restrictions, record title fallback logic, and navigation sorting for many modules.

## 3. Cross-Module Imports In Core Models

### `Modules\Core\Models\Tenant`

`Tenant` imports 76 feature-module model classes across 8 modules:

| Module | Import Count | Imported Model Area |
| --- | ---: | --- |
| Campus | 11 | faculty, programs, courses, lecturers, study plans/results, feeder logs, theses |
| Employee | 12 | employees, attendance logs, contracts, payroll, shifts, KPI |
| Enrollment | 5 | admission periods, applicants, exams, registrations |
| Finance | 8 | accounts, invoices, payments, journals, budgets, tuition |
| Library | 8 | books, copies, members, loans, fines, policies |
| Monitoring | 2 | audit logs, file uploads |
| Procurement | 14 | vendors, requisitions, RFQs, purchase orders, receipts, bills |
| School | 16 | curricula, subjects, students, teachers, classes, attendance, assessments, grades, violations, achievements |

The relationships are all implemented directly on `Tenant`, mostly as `hasMany()` methods. This makes `Tenant` a cross-module index of the entire ERP domain.

### `Modules\Core\Models\User`

`User` imports 28 feature-module model classes across 8 modules:

| Module | Import Count | Imported Model Area |
| --- | ---: | --- |
| Campus | 3 | lecturers, study plans, feeder logs |
| Employee | 4 | employees, attendance logs, leave requests, KPI scores |
| Enrollment | 2 | exam results, registrations |
| Finance | 3 | budgets, journal entries, payments |
| Library | 2 | members, loans |
| Monitoring | 2 | audit logs, file uploads |
| Procurement | 5 | requisitions, RFQs, purchase orders, receipts, bills |
| School | 7 | students, teachers, attendance, violations, assessment answers, grades, achievements |

These are mostly actor/auditor relationships such as `verifiedPayments()`, `approvedBudgets()`, `approvedPurchaseOrders()`, `gradedStudentAnswers()`, and `syncedFeederLogs()`.

## 4. Risky God Model Areas In Tenant And User

### Tenant

Risky areas:

- Cross-module relationship catalog: `Tenant` contains direct relationship methods for many feature modules.
- Billing state and subscription behavior: status checks, subscription dates, plan relationship, logs, Midtrans customer id, and billing limits all live on the tenant model.
- Platform identity and branding: domain, subdomain, logo, colors, locale, timezone, metadata, and settings are mixed with ERP relationships.
- Activity logging: tenant changes are logged, which is good, but broad future changes to fillable/casts/status behavior can affect audit semantics.

Recommended approach:

- Keep existing methods for backward compatibility until usages are mapped.
- Add module-owned relationship accessors or query services later, then delegate old methods to those services before removing anything.
- Do not remove any `Tenant` relationship until all resource managers, policies, reports, commands, exports, and tests using it have been found.

### User

Risky areas:

- Filament identity concerns, MFA, tenant membership, roles, Sanctum API tokens, localization, and activity logging are all on one model.
- Cross-module actor relationships make `User` depend on many feature modules.
- `canAccessPanel()` contains platform/admin/parent panel access logic in one method and references tenant roles, global super admin state, and parent-student access.
- `getTenants()` and `getDefaultTenant()` are operationally important for Filament tenancy and should be treated as high-risk.

Recommended approach:

- Keep `User` as the authenticatable model.
- Extract only additive query helpers or concerns first; do not change authentication, MFA, role, or tenant access behavior in the first refactor phases.
- Move feature-module actor relationships into module-level query objects or optional traits only after usages are known.

## 5. Responsibilities Currently Inside ModuleResource

`Modules\Core\Filament\Support\ModuleResource` currently handles:

1. Navigation icon inference from resource class names.
2. Filament tenant-scope detection by checking whether the model has the configured tenant ownership relationship.
3. Eloquent query customization that removes `SoftDeletingScope`.
4. Global-resource mutation restrictions for create/edit/delete/restore/force-delete operations.
5. Module visibility checks based on enabled `TenantModule` rows, with cache.
6. Navigation sort order for Core, Global, School, Campus, Workflow, Enrollment, CMS, Donation, Training, Sales, Marketplace, Employee, Finance, Inventory, Procurement, Library, Monitoring, Legal, Asset, DMS, Helpdesk, Facility, EOffice, ItOps, Transport, Boarding, Cafeteria, and PhysicalSecurity.
7. Navigation group localization through `FilamentUi::module()`.
8. Navigation label and model label localization through `FilamentUi`.
9. Record title fallback logic across common field names and a `user` relationship fallback.
10. Module name inference from namespace.
11. Proper-case formatting for labels.

This class is valuable as a compatibility layer, but it is too broad to keep growing. The highest-risk part is not one individual method; it is that every module resource inherits all behavior whether it needs it or not.

## 6. Recommended Target Architecture

The target should be evolutionary, not a rewrite:

- Keep Core as the owner of shared primitives: `Tenant`, `User`, `Organization`, `Department`, roles, tenant membership, tenant modules, subscription plans/logs, settings, and tenant context.
- Feature modules should own their domain relationships. A School relationship should live in School code, a Finance actor relationship in Finance code, and so on.
- Core should expose stable contracts and compatibility helpers, not import every feature model directly.
- Module-specific aggregate queries should move behind small query classes, relation resolvers, or module service classes.
- `ModuleResource` should remain as a base class during migration, but responsibilities should be split gradually into smaller support classes:
  - navigation metadata resolver,
  - tenant module visibility policy,
  - global mutation policy,
  - record title resolver,
  - resource label/localization resolver,
  - navigation sort registry.
- Filament tenant isolation should remain centered on Filament tenancy plus persistent tenant middleware. This matches Filament 5 guidance for tenant-aware middleware and tenant-aware resource testing.
- API tenant context should remain token-bound for now, with tests proving that a token from tenant A cannot read or write tenant B data.
- Billing webhook hardening should be a later focused phase with its own tests for signature validation, gross amount validation, idempotency, duplicate notification handling, unknown orders, and raw payload logging.

## 7. Refactor Phases With Low-Risk Order

### Phase 0: Lock Safety Nets

- Add or strengthen tests for tenant scope isolation, API tenant context, Filament tenant resource access, and billing webhook behavior.
- Run only focused tests first:
  - `php artisan test --compact tests/Feature/TenantScopeIsolationTest.php`
  - `php artisan test --compact tests/Feature/ApiResourceReadTest.php`
  - `php artisan test --compact tests/Feature/BillingEngineTest.php`

### Phase 1: Inventory Usages Before Moving Anything

- Use `rg` to find every call to the relationship methods on `Tenant` and `User`.
- Classify each method as used by Filament relation managers, policies, resources, seeders, commands, tests, reports, or unused.
- Do not delete unused-looking relationships until this usage map includes dynamic references and relation manager conventions.

### Phase 2: Add Compatibility Resolvers

- Introduce additive support classes that do not change behavior yet:
  - `TenantRelationshipResolver` or module-specific query classes.
  - `UserActorRelationshipResolver` or module-specific query classes.
  - `ModuleNavigationMetadata` for icons and sort order.
  - `ModuleVisibility` for tenant module enablement checks.
- Keep existing methods on `Tenant`, `User`, and `ModuleResource` delegating to the new classes.

### Phase 3: Move ModuleResource Responsibilities Gradually

- Extract navigation icon resolution first because it is UI-only and easy to test with deterministic class names.
- Extract navigation sort registry next.
- Extract record-title resolution after checking resources that rely on the current fallback order.
- Extract mutation policy and tenant-scope behavior last because these affect authorization and data isolation.

### Phase 4: Move Cross-Module Relationships Behind Module-Owned APIs

- Start with one low-risk module, preferably Monitoring or Library, not School or Finance.
- Keep the old `Tenant`/`User` relationship method as a compatibility wrapper.
- Add tests proving old callers still work.
- Repeat module by module.

### Phase 5: Harden API Tenant Context

- Add explicit tests for:
  - missing token tenant id,
  - token tenant A attempting to access tenant B records,
  - write endpoints auto-filling the token tenant,
  - `CurrentTenant` restoration between requests/tests.
- Only after tests pass should middleware behavior be changed.

### Phase 6: Harden Billing Webhook

- Add raw payload logging and idempotency records before changing activation behavior.
- Validate Midtrans signature and expected gross amount.
- Treat duplicate paid webhooks as no-op success.
- Keep old `handleWebhookNotification(array $notification)` as a compatibility entry point until all controllers/tests call the hardened method.

## 8. Files That Should Not Be Touched Yet

These files are high-risk and should not be modified until their own focused phase has tests and a usage map:

- `Modules/Core/app/Models/Tenant.php`
- `Modules/Core/app/Models/User.php`
- `Modules/Core/app/Filament/Support/ModuleResource.php`
- `Modules/Core/app/Models/Concerns/BelongsToTenant.php`
- `app/Scopes/TenantScope.php`
- `app/Support/CurrentTenant.php`
- `app/Http/Middleware/ResolveApiTenant.php`
- `app/Services/BillingService.php`
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Providers/Filament/PlatformPanelProvider.php`
- `routes/api.php`
- module resource classes under `Modules/*/app/Filament/Resources`
- module model classes under `Modules/*/app/Models`
- migrations and production configuration
- `.env`, credentials, secrets, database dumps, and deployment config

## Verification Notes For This Phase

This phase intentionally changes documentation only. No application code, dependencies, environment files, migrations, or production configuration were changed.

Suggested checks after this document-only change:

```bash
git diff -- docs/refactor/foundationos-refactor-audit.md
php artisan test --compact tests/Feature/TenantScopeIsolationTest.php
php artisan test --compact tests/Feature/ApiResourceReadTest.php
php artisan test --compact tests/Feature/BillingEngineTest.php
vendor/bin/pint --dirty --format agent
```

Because no PHP files were edited in this phase, Pint and the focused tests are optional sanity checks rather than required proof of changed runtime behavior.
