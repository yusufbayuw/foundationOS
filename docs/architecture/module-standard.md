# foundationOS Module Standard

This guide defines the preferred module shape for foundationOS. Apply it gradually. Existing modules do not need large moves just to comply; compatibility and reviewable changes are more important than structural churn.

## Recommended Folder Structure

```text
Modules/{Module}/
  app/
    Actions/
    Data/
    Filament/
      Exports/
      Imports/
      Pages/
      Resources/{PluralResource}/
        Pages/
        RelationManagers/
        Schemas/
        Tables/
      Widgets/
    Http/
      Controllers/
      Resources/
      Requests/
    Models/
    Observers/
    Policies/
    Providers/
    Services/
    Support/
  config/
    config.php
    module.php
    navigation.php
  database/
    factories/
    migrations/
    seeders/
  lang/
    en/{module}.php
    id/{module}.php
  resources/
    views/
  routes/
    api.php
    web.php
  tests/
    Feature/
    Unit/
  module.json
```

Use only the folders a module actually needs. Empty placeholders are acceptable when they match the generated module structure, but new code should not create unused architecture layers.

## Placement Rules

Models go in `app/Models` and should own persistence concerns, casts, relationships, scopes, and small domain predicates. Avoid adding cross-module convenience relationships unless there is a real usage and no module-level query/service alternative.

Filament resources go in `app/Filament/Resources/{PluralName}` with the resource class at the resource root, page classes in `Pages`, schema builders in `Schemas`, table builders in `Tables`, and relation managers in `RelationManagers`.

Actions go in `app/Actions` for single-purpose write operations or orchestration that would otherwise bloat controllers, pages, observers, or services.

Services go in `app/Services` for reusable domain workflows, integrations, reporting, calculations, and query coordination. Keep services tenant-aware by accepting explicit tenant context or using existing tenant-scoped models intentionally.

Policies go in `app/Policies` and should use the app's existing permission naming convention. Policies should not run broad cross-tenant queries without an explicit reason.

Data objects go in `app/Data` when a workflow needs typed structured input/output across controllers, services, jobs, or actions. Prefer plain arrays for narrow private method boundaries.

Support classes go in `app/Support` for low-level helpers local to the module. If a helper becomes shared by multiple modules, move it deliberately into Core after checking usages.

Config goes in `config/config.php` for legacy/default module config, `config/module.php` for standard metadata, and `config/navigation.php` for navigation metadata. Do not put secrets in module config.

Translations go in `lang/en/{module}.php` and `lang/id/{module}.php`. `FilamentUi` checks module lang files first, then Core lang files, then the legacy phrase map.

Tests go in `tests/Feature` for workflows, HTTP/API, Filament, tenancy, policies, and integration behavior. Use `tests/Unit` for isolated pure PHP logic.

## Naming Conventions

Use PascalCase class names and singular model names: `Student`, `SchoolClass`, `StudentRiskScore`.

Use plural resource directories and resource class names ending in `Resource`: `Students/StudentResource.php`, `SchoolClasses/SchoolClassResource.php`.

Use descriptive service and action names: `ReportCardService`, `GenerateReportCard`, `ScheduleConflictChecker`.

Use snake_case database columns and route parameters. Use lower snake_case or kebab-case config keys consistently within the same config file.

Use module config keys under the module alias, for example `school.name` from `config/config.php` and `school.module.domain` only if that file is merged under a nested key in a future provider change. Until then, direct include tests are acceptable for metadata files.

## Tenant-Scoped Model Rules

Tenant-owned models must include `tenant_id` in the table and `$fillable` list.

Tenant-owned models should use `Modules\Core\Models\Concerns\BelongsToTenant` unless there is a documented reason not to.

Use `CurrentTenant` context for runtime scoping. When creating records in tenant-aware code, allow `BelongsToTenant` to fill `tenant_id` where possible.

Use `withoutTenantScope()` only in explicit cross-tenant workflows such as seeders, console jobs, platform administration, imports, billing, or reconciliation. Keep those call sites visible and tested.

Do not expose cross-tenant records in API or Filament panels unless the panel is explicitly global/platform scoped.

## Filament Resource Rules

Module resources should extend `Modules\Core\Filament\Support\ModuleResource`.

Keep resource classes thin. Put forms in `Schemas`, tables in `Tables`, and page-specific behavior in `Pages`.

Use `TenantField::make()` for hidden `tenant_id` and `TenantField::organizationSelect()` for organization selectors when the model belongs to a tenant organization.

Use `FilamentUi::field()` and `FilamentUi::text()` for labels, section headings, and navigation labels. Add module lang entries for labels that should not rely on the legacy phrase map.

Prefer controlled `Select` values, enums, or value objects for stable status/type fields. Avoid free-text status fields unless the business process truly needs arbitrary values.

Do not hardcode module visibility in resources. Visibility should continue to flow through `ModuleResource` and Core navigation support.

## API Controller and Resource Rules

API controllers belong in `app/Http/Controllers` for module-specific endpoints or `app/Http/Controllers/Api/v1` for shared versioned API surfaces.

Use Eloquent API Resources for public JSON shapes when returning models or collections.

Respect `ResolveApiTenant` and tenant-scoped models. Show endpoints should return `404` for records outside the token tenant.

Use Form Requests for complex validation. Simple read-only filters may stay in the controller if they follow existing `ApiController` conventions.

Do not return raw exception messages from production-facing API or webhook endpoints.

## Permission Naming Rules

Use the existing Shield-style permission format:

```text
ViewAny:{Model}
View:{Model}
Create:{Model}
Update:{Model}
Delete:{Model}
DeleteAny:{Model}
Restore:{Model}
RestoreAny:{Model}
ForceDelete:{Model}
ForceDeleteAny:{Model}
Replicate:{Model}
Reorder:{Model}
```

Policy methods should map directly to those permission names unless the module has a documented domain rule.

Tenant role permissions should be scoped to tenant operations. Platform/global permissions should stay separate from tenant roles.

## Navigation Config Rules

Use `config/navigation.php` for module-owned navigation metadata such as module group, icon, and desired resource/page order.

Current behavior still uses Core navigation support, especially `NavigationIconResolver`, `NavigationSortRegistry`, `ModuleVisibility`, and `ModuleResource`. Do not assume module navigation config is live until a later integration phase wires it in.

Navigation labels should come from `FilamentUi` and module lang files, not hardcoded display text.

Navigation icons should use Filament `Heroicon` enum values where possible.

Navigation sorting should reflect operational flow: setup/master data first, daily transactions next, reports/analytics last.

## Testing Requirements

Every production code change must have a focused test or a clear reason why existing tests cover it.

Minimum checks for module changes:

```bash
php -l path/to/changed.php
vendor/bin/pint --dirty --format agent
php artisan test --compact tests/Feature/RelevantTest.php
```

Add or update tests for:

- Tenant isolation and `withoutTenantScope()` behavior.
- Filament form/table/resource behavior when labels, validation, or tenant fields change.
- API token tenant context for versioned API endpoints.
- Policy permission decisions for new resources.
- Services/actions that mutate state or integrate with external systems.

Avoid broad test data setup that bypasses tenant context accidentally. Prefer factories where available; add minimal factories when missing.

## Applying This Standard Gradually

For existing modules, start by adding `config/module.php`, `config/navigation.php`, and lang entries. Then move repeated business workflows into actions/services only when touching that workflow for a real change.

Do not move many files in a single standardization pass. File moves should be small, backed by usage search, and verified by targeted tests.
