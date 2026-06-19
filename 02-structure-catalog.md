# 02 — Structure Catalog

**REAP Stage:** 03 — Structure Discovery  
**Artifact Status:** Verified  
**Generated:** 2026-06-19T12:00:00Z  
**Commit:** `9137e4b`  
**Branch:** `cursor/use-case-model-0a94`  
**Purpose:** Memetakan folder, package, namespace, modul, dan entrypoint tanpa analisis bisnis.

**Upstream artifacts:** `00-repository-manifest.md` (Stage 01), `01-evidence-registry.json` (Stage 02)

---

## 1. Structure Catalog

### 1.1 Root Folders

| Path | Role | Confidence | Evidence ID |
|------|------|------------|-------------|
| `app/` | Core Laravel application layer (Filament panels, imports, Moodle integration, jobs) | VERIFIED | EV-S03-001 |
| `Modules/` | 45 feature modules (nwidart/coolsam modular monolith) | VERIFIED | EV-S03-002 |
| `bootstrap/` | Laravel bootstrap (`app.php`, `providers.php`) | VERIFIED | EV-S03-003 |
| `config/` | Application configuration (24 PHP files) | VERIFIED | EV-S03-004 |
| `database/` | Core migrations, factories, seeders | VERIFIED | EV-S03-005 |
| `routes/` | Global HTTP/API/console routing (`web.php`, `api.php`, `console.php`) | VERIFIED | EV-S03-006 |
| `public/` | Web document root (`index.php`) | VERIFIED | EV-S03-007 |
| `resources/` | Blade views, CSS/JS (Vite inputs) | VERIFIED | EV-S03-008 |
| `tests/` | PHPUnit feature/unit tests (173 PHP) | VERIFIED | EV-S03-009 |
| `scripts/` | Extraction & lint utilities (21 tracked files) | VERIFIED | EV-S03-010 |
| `docs/` | Discovery catalogs (`docs/catalogs/*.json`) | VERIFIED | EV-S03-011 |
| `mobile/` | Capacitor mobile shell config only | VERIFIED | EV-S03-012 |
| `storage/` | Runtime cache, logs, local extracts (mostly gitignored) | VERIFIED | EV-S03-013 |
| `vendor/` | Composer dependencies (excluded from audit) | VERIFIED | EV-S03-014 |
| `node_modules/` | NPM dependencies (excluded from audit) | VERIFIED | EV-S03-015 |

**Agent / IDE metadata (not application source):** `.agents/`, `.claude/`, `.codex/`, `.cursor/`, `.gemini/`, `.github/`

---

### 1.2 Packages

#### 1.2.1 Composer (`composer.json`)

| Field | Value | Confidence |
|-------|-------|------------|
| Composer `name` | `laravel/laravel` | VERIFIED |
| Product name | **FoundationOS** (`README.md`) | VERIFIED |
| PHP runtime | `^8.4` | VERIFIED |
| Laravel | `^13.0` | VERIFIED |
| Filament | `^5.4` | VERIFIED |
| Modular stack | `coolsam/modules` `^5.1` (nwidart-compatible) | VERIFIED |
| Auth / permissions | `bezhansalleh/filament-shield` `^4.2`, Spatie via Shield | VERIFIED |
| API auth | `laravel/sanctum` `^4.3` | VERIFIED |
| Activity log | `spatie/laravel-activitylog` `^5.0` | VERIFIED |
| PDF | `barryvdh/laravel-dompdf` `^3.1` | VERIFIED |
| Workflow rules | `jwadhams/json-logic-php` `^1.5` | VERIFIED |
| Payments | `midtrans/midtrans-php` `^2.6` | VERIFIED |
| QR | `simplesoftwareio/simple-qrcode` `^4.2` | VERIFIED |
| Observability | `laravel/pulse` `^1.7` | VERIFIED |

**Composer scripts (entry-related):**

| Script | Command chain | Role |
|--------|---------------|------|
| `composer run dev` | `php artisan serve` + queue + pail + `npm run dev` | Local dev orchestration |
| `composer run test` | `php artisan test` | Test runner |
| `composer run lint:translations` | `scripts/lint-translations.php` | Translation linter |

#### 1.2.2 NPM (`package.json`)

| Field | Value | Confidence |
|-------|-------|------------|
| Build tool | Vite `^8.0.0` | VERIFIED |
| CSS | Tailwind CSS `^4.3.0` | VERIFIED |
| Laravel bridge | `laravel-vite-plugin` `^3.0.0` | VERIFIED |

**Vite inputs** (`vite.config.js`): `resources/css/app.css`, `resources/js/app.js`, `resources/js/workflow-designer.js`, `resources/css/filament/admin/theme.css`

#### 1.2.3 Module packages

| Metric | Count | Confidence | Evidence ID |
|--------|------:|------------|-------------|
| `Modules/*/module.json` | 45 | VERIFIED | EV-S03-016 |
| `Modules/*/composer.json` | 44 | VERIFIED | EV-S03-017 |
| Missing `composer.json` | **Exam** only | VERIFIED | EV-S03-018 |
| `modules_statuses.json` enabled flags | 45 / 45 `true` | VERIFIED | EV-S03-019 |

---

### 1.3 Namespaces (PSR-4)

| Namespace prefix | Path | Scope | Confidence |
|------------------|------|-------|------------|
| `App\` | `app/` | Application shell, Filament panel providers, imports, Moodle | VERIFIED |
| `Modules\{Name}\` | `Modules/{Name}/app/` | One prefix per feature module (45 entries) | VERIFIED |
| `Database\Factories\` | `database/factories/` | Core factories | VERIFIED |
| `Database\Seeders\` | `database/seeders/` | Core seeders | VERIFIED |
| `Tests\` | `tests/` | PHPUnit (dev autoload) | VERIFIED |

**Module namespace pattern:** `Modules\{PascalCaseModule}\` → physical path `Modules/{PascalCaseModule}/app/`

**Examples:**

| Module | Root namespace | Primary provider |
|--------|----------------|------------------|
| Core | `Modules\Core\` | `Modules\Core\Providers\CoreServiceProvider` |
| School | `Modules\School\` | `Modules\School\Providers\SchoolServiceProvider` |
| Workflow | `Modules\Workflow\` | `Modules\Workflow\Providers\WorkflowServiceProvider` |

**Filament resource base (cross-cutting):** `Modules\Core\Filament\Support\ModuleResource` — extended by 257 resource classes across modules.

---

### 1.4 Application Layer (`app/`)

| Subfolder | Responsibility | Confidence |
|-----------|----------------|------------|
| `Console/Commands/` | App-level Artisan commands (58 PHP files) | VERIFIED |
| `Filament/Imports/` | ~132 CSV importers (`BaseModelImporter` subclasses) | VERIFIED |
| `Filament/Pages/` | Shared admin pages (billing, dashboard, tenancy) | VERIFIED |
| `Filament/Platform/` | Platform panel pages | VERIFIED |
| `Filament/Parent/` | Parent panel pages | VERIFIED |
| `Http/Controllers/` | Web + `Api/v1`, `Api/v2` controllers | VERIFIED |
| `Http/Middleware/` | Tenant, idempotency, API version middleware | VERIFIED |
| `Integrations/Moodle/` | Moodle client, mapper, outbox | VERIFIED |
| `Jobs/` | Queue jobs (e.g. Moodle sync outbox) | VERIFIED |
| `Observers/` | Model observers triggering side effects | VERIFIED |
| `Providers/Filament/` | Three Filament `PanelProvider` classes | VERIFIED |
| `Services/` | App-level services (billing, workflow helpers) | VERIFIED |

`app/Models/User.php` is a **bridge** to `Modules\Core\Models\User` (ecosystem compatibility only).

---

### 1.5 Canonical Module Internal Tree

**Reference module:** `Modules/Core/` (full stack)

```
Modules/{Name}/
├── module.json              # nwidart manifest (name, providers)
├── composer.json            # optional per-module (44/45)
├── app/
│   ├── Filament/
│   │   ├── Resources/{Model}/   # Resource, Pages, Schemas, Tables
│   │   ├── Pages/
│   │   └── Support/
│   ├── Http/Controllers/
│   ├── Http/Middleware/
│   ├── Models/
│   ├── Policies/
│   ├── Providers/
│   │   ├── {Name}ServiceProvider.php   # extends ModuleServiceProvider
│   │   ├── RouteServiceProvider.php
│   │   └── EventServiceProvider.php
│   ├── Services/
│   ├── Enums/
│   └── Console/Commands/    # sparse (5 module commands total)
├── config/
├── database/migrations|factories|seeders/
├── routes/web.php|api.php
├── resources/views/
├── lang/                    # Core, School only
└── tests/
```

**Registration chain:**

1. `module.json` → `providers[]` lists `{Name}ServiceProvider`
2. `{Name}ServiceProvider` extends `Nwidart\Modules\Support\ModuleServiceProvider`
3. Nested providers typically include `RouteServiceProvider`, `EventServiceProvider`
4. Filament admin panel loads module plugins via `Coolsam\Modules\ModulesPlugin` (`config/filament-modules.php` mode `BOTH`, `auto-register-plugins` true)

---

### 1.6 Entrypoints

| Kind | Path / URI | Mechanism | Confidence | Evidence ID |
|------|------------|-----------|------------|-------------|
| **HTTP (web)** | `public/index.php` | `bootstrap/app.php` → `routes/web.php` | VERIFIED | EV-S03-020 |
| **HTTP (API)** | `api/*` prefix | `bootstrap/app.php` → `routes/api.php` + module `routes/api.php` | VERIFIED | EV-S03-021 |
| **Health** | `GET /up` | `bootstrap/app.php` `health: '/up'` | VERIFIED | EV-S03-022 |
| **CLI** | `artisan` | `bootstrap/app.php` + `routes/console.php` + commands | VERIFIED | EV-S03-023 |
| **Filament admin** | `/admin` | `AdminPanelProvider` (`panel id: admin`, default) | VERIFIED | EV-S03-024 |
| **Filament platform** | `/platform` | `PlatformPanelProvider` | VERIFIED | EV-S03-025 |
| **Filament parent** | `/parent` | `ParentPanelProvider` | VERIFIED | EV-S03-026 |
| **Queue worker** | `php artisan queue:listen` | `composer run dev` orchestration | VERIFIED | EV-S03-027 |
| **Scheduler** | `routes/console.php` | 28 `Schedule::command` entries | VERIFIED | EV-S03-028 |
| **Vite dev/build** | `npm run dev` / `npm run build` | `vite.config.js` | VERIFIED | EV-S03-029 |
| **Mobile shell** | Capacitor | `mobile/capacitor.config.json` → `webDir: ../public` | VERIFIED | EV-S03-030 |

**Bootstrap provider registration** (`bootstrap/providers.php`):

```php
AppServiceProvider::class,
AdminPanelProvider::class,
ParentPanelProvider::class,
PlatformPanelProvider::class,
```

Module providers are **not** listed here; they load via nwidart/coolsam module discovery (`module.json`).

**Global route files:**

| File | Role |
|------|------|
| `routes/web.php` | Welcome, billing webhooks, locale switch |
| `routes/api.php` | Sanctum v1 API, OpenAPI, webhooks, module includes |
| `routes/console.php` | Scheduled commands |

**Module route files (on disk):** 44 `web.php`, 14 `api.php` under `Modules/*/routes/`

**Registered Artisan commands:** 329 total (`php artisan list --raw`); 58 in `app/Console/Commands/`, 5 in `Modules/*/Console/Commands/`

---

## 2. Module Candidates

### 2.1 Laravel Feature Modules (45)

All directories under `Modules/` are nwidart modules with `module.json`. All enabled in `modules_statuses.json`.

| # | Module | Namespace | Service provider | Subdirs present | ModuleResource count | Structural tier |
|---|--------|-----------|------------------|-----------------|---------------------:|-----------------|
| 1 | Ai | `Modules\Ai\` | `AiServiceProvider` | app, database | 1 | **Minimal** |
| 2 | Alumni | `Modules\Alumni\` | `AlumniServiceProvider` | app, config, database, resources, routes, tests | 9 | Full |
| 3 | Asset | `Modules\Asset\` | `AssetServiceProvider` | app, config, database, resources, routes, tests | 7 | Full |
| 4 | Boarding | `Modules\Boarding\` | `BoardingServiceProvider` | app, config, database, resources, routes, tests | 7 | Full |
| 5 | Cafeteria | `Modules\Cafeteria\` | `CafeteriaServiceProvider` | app, config, database, resources, routes, tests | 8 | Full |
| 6 | Campus | `Modules\Campus\` | `CampusServiceProvider` | app, config, database, resources, routes, tests | 6 | Full |
| 7 | Capacity | `Modules\Capacity\` | `CapacityServiceProvider` | app, config, database, resources, routes, tests | 3 | Full |
| 8 | Clinic | `Modules\Clinic\` | `ClinicServiceProvider` | app, config, database, resources, routes, tests | 9 | Full |
| 9 | Cms | `Modules\Cms\` | `CmsServiceProvider` | app, config, database, resources, routes, tests | 10 | Full |
| 10 | Consulting | `Modules\Consulting\` | `ConsultingServiceProvider` | app, database, resources, routes | 6 | **Lean** (no config/tests) |
| 11 | Core | `Modules\Core\` | `CoreServiceProvider` | app, config, database, lang, resources, routes, tests | 10 | **Foundation** |
| 12 | Counseling | `Modules\Counseling\` | `CounselingServiceProvider` | app, config, database, resources, routes, tests | 7 | Full |
| 13 | Dms | `Modules\Dms\` | `DmsServiceProvider` | app, config, database, resources, routes, tests | 4 | Full |
| 14 | Donation | `Modules\Donation\` | `DonationServiceProvider` | app, config, database, resources, routes, tests | 7 | Full |
| 15 | EducationQa | `Modules\EducationQa\` | `EducationQaServiceProvider` | app, config, database, resources, routes, tests | 8 | Full |
| 16 | Employee | `Modules\Employee\` | `EmployeeServiceProvider` | app, config, database, resources, routes, tests | 1 | Full (sparse Filament) |
| 17 | Enrollment | `Modules\Enrollment\` | `EnrollmentServiceProvider` | app, config, database, resources, routes, tests | 5 | Full |
| 18 | EOffice | `Modules\EOffice\` | `EOfficeServiceProvider` | app, config, database, resources, routes, tests | 5 | Full |
| 19 | Event | `Modules\Event\` | `EventModuleServiceProvider` | app, config, database, resources, routes, tests | 10 | Full |
| 20 | Exam | `Modules\Exam\` | `ExamServiceProvider` | app, config, database, resources, routes | 0 | **Lean** (no tests, no composer.json) |
| 21 | Facility | `Modules\Facility\` | `FacilityServiceProvider` | app, config, database, resources, routes, tests | 8 | Full |
| 22 | Finance | `Modules\Finance\` | `FinanceServiceProvider` | app, config, database, resources, routes, tests | 1 | Full (sparse Filament) |
| 23 | Global | `Modules\Global\` | `GlobalServiceProvider` | app, config, database, resources, routes, tests | 0 | Full (reference data, no Filament resources) |
| 24 | Helpdesk | `Modules\Helpdesk\` | `HelpdeskServiceProvider` | app, config, database, resources, routes, tests | 6 | Full |
| 25 | InternalAudit | `Modules\InternalAudit\` | `InternalAuditServiceProvider` | app, config, database, resources, routes, tests | 9 | Full |
| 26 | Inventory | `Modules\Inventory\` | `InventoryServiceProvider` | app, config, database, resources, routes, tests | 7 | Full |
| 27 | IsoCompliance | `Modules\IsoCompliance\` | `IsoComplianceServiceProvider` | app, config, database, resources, routes, tests | 7 | Full |
| 28 | ItOps | `Modules\ItOps\` | `ItOpsServiceProvider` | app, config, database, resources, routes, tests | 4 | Full |
| 29 | KpiEnterprise | `Modules\KpiEnterprise\` | `KpiEnterpriseServiceProvider` | app, config, database, resources, routes, tests | 6 | Full |
| 30 | Legal | `Modules\Legal\` | `LegalServiceProvider` | app, config, database, resources, routes, tests | 4 | Full |
| 31 | Library | `Modules\Library\` | `LibraryServiceProvider` | app, config, database, resources, routes, tests | 1 | Full (sparse Filament) |
| 32 | Marketplace | `Modules\Marketplace\` | `MarketplaceServiceProvider` | app, database, routes | 8 | **Lean** (no config/resources/tests) |
| 33 | MerchOrder | `Modules\MerchOrder\` | `MerchOrderServiceProvider` | app, config, database, resources, routes, tests | 6 | Full |
| 34 | Messaging | `Modules\Messaging\` | `MessagingServiceProvider` | app, config, database, resources, routes, tests | 3 | Full |
| 35 | Monitoring | `Modules\Monitoring\` | `MonitoringServiceProvider` | app, config, database, resources, routes, tests | 2 | Full |
| 36 | PhysicalSecurity | `Modules\PhysicalSecurity\` | `PhysicalSecurityServiceProvider` | app, config, database, resources, routes, tests | 8 | Full |
| 37 | Printing | `Modules\Printing\` | `PrintingServiceProvider` | app, database, routes | 8 | **Lean** (no config/resources/tests) |
| 38 | Procurement | `Modules\Procurement\` | `ProcurementServiceProvider` | app, config, database, resources, routes, tests | 0 | Full (domain via workflow/services) |
| 39 | Property | `Modules\Property\` | `PropertyServiceProvider` | app, database, resources, routes | 6 | **Lean** (no config/tests) |
| 40 | Risk | `Modules\Risk\` | `RiskServiceProvider` | app, config, database, resources, routes, tests | 7 | Full |
| 41 | Sales | `Modules\Sales\` | `SalesServiceProvider` | app, config, database, resources, routes, tests | 4 | Full |
| 42 | School | `Modules\School\` | `SchoolServiceProvider` | app, config, database, lang, resources, routes, tests | 5 | Full |
| 43 | Training | `Modules\Training\` | `TrainingServiceProvider` | app, config, database, resources, routes, tests | 9 | Full |
| 44 | Transport | `Modules\Transport\` | `TransportServiceProvider` | app, config, database, resources, routes, tests | 9 | Full |
| 45 | Workflow | `Modules\Workflow\` | `WorkflowServiceProvider` | app, config, database, resources, routes, tests | 6 | Full |

**Aggregate Filament:** 257 classes extend `ModuleResource` (sum of per-module counts = 257).  
**Tier legend:**

- **Foundation** — tenancy, users, shared Filament base (`Core`)
- **Full** — standard six-folder module layout
- **Lean** — missing one or more of `config/`, `resources/`, `tests/`
- **Minimal** — only `app/` + `database/` (`Ai`)

### 2.2 Non-Module Structural Candidates

| Path | Candidate type | Namespace | Entry? | Notes | Confidence |
|------|----------------|-----------|--------|-------|------------|
| `mobile/` | Mobile shell | N/A (Capacitor) | Indirect (`public/`) | Single file `capacitor.config.json`; not a PHP module | VERIFIED |
| `scripts/` | Dev/audit utilities | N/A | CLI only | `extract-*.php`, `build-evidence-registry.php`, linters | VERIFIED |
| `docs/` | Documentation & JSON catalogs | N/A | None | `docs/catalogs/authorization-matrix.json`, `api-routes-catalog.json` | VERIFIED |
| `resources/` | Frontend assets | N/A | Vite | Blade + CSS/JS sources | VERIFIED |

These are **not** registered in `modules_statuses.json` and do not have `module.json`.

### 2.3 Domain Clustering (navigation grouping only)

Filament module clusters enabled (`config/filament-modules.php` `clusters.enabled = true`). Physical module boundaries remain **one folder per module**; clustering is a **UI grouping** concern, not a separate package boundary.

**INFERRED** education vs operations split (from module naming only — no business semantics asserted):

| Cluster (informal) | Modules |
|--------------------|---------|
| Core platform | Core, Global, Monitoring, Workflow, Messaging, Cms |
| K-12 / campus academics | School, Campus, Enrollment, Exam, EducationQa, Library |
| HR / finance / ops | Employee, Finance, Procurement, Inventory, Asset, Facility |
| Student life / services | Boarding, Cafeteria, Transport, Clinic, Counseling, Donation |
| Governance / compliance | Legal, Risk, InternalAudit, IsoCompliance, ItOps, PhysicalSecurity |
| Commercial / extensions | Sales, Marketplace, MerchOrder, Consulting, Property, Printing, Ai |

---

## 3. Dependency Notes

### 3.1 Internal dependency graph (structural)

```
public/index.php
    └── bootstrap/app.php
            ├── routes/web.php, api.php, console.php
            ├── bootstrap/providers.php (App + 3 Filament panels)
            └── vendor/autoload.php
                    ├── App\ (app/)
                    └── Modules\{Name}\ (Modules/{Name}/app/)
                            └── module.json → {Name}ServiceProvider
                                    ├── RouteServiceProvider → routes/*.php
                                    ├── EventServiceProvider → Events/Listeners
                                    └── Filament resources → ModuleResource (Core)
```

**Hard structural dependencies (verified files, not runtime coupling):**

| From | To | Relationship |
|------|-----|--------------|
| All modules | `Modules\Core\Filament\Support\ModuleResource` | Filament resource base class |
| All modules | `Modules\Core\Models\Tenant` | Filament `->tenant()` on admin panel |
| `app/Models/User` | `Modules\Core\Models\User` | Class extends bridge |
| `AdminPanelProvider` | `Coolsam\Modules\ModulesPlugin`, `Nwidart\Modules\Facades\Module` | Module plugin registration |
| `app/Filament/Imports/*` | Module models | CSV import targets |
| `app/Integrations/Moodle/*` | Campus/School models (observers) | Outbound sync triggers |
| `routes/api.php` | `Modules\EOffice`, `Modules\Messaging`, `Modules\Exam` controllers | Cross-module HTTP entry |

### 3.2 External package dependencies (runtime)

| Package | Structural role |
|---------|-----------------|
| `laravel/framework` | HTTP kernel, Eloquent, queue, scheduler |
| `filament/filament` | Admin UI framework (3 panels + module plugins) |
| `coolsam/modules` | Module discovery, Filament plugin wiring |
| `bezhansalleh/filament-shield` | Permission generation per `ModuleResource` |
| `laravel/sanctum` | API token auth (`routes/api.php`) |
| `spatie/laravel-activitylog` | Audit trail (Monitoring module) |
| `laravel/pulse` | Performance dashboard |

### 3.3 Tenancy boundary (structural marker)

Shared-database multi-tenancy: operational tables carry `tenant_id`. Trait `Modules\Core\Models\Concerns\BelongsToTenant` is the canonical scoping mechanism. **No** `stancl/tenancy` package in `composer.json`.

### 3.4 Cross-artifact dependencies (REAP pipeline)

| Artifact | Depends on |
|----------|------------|
| `02-structure-catalog.md` (this) | `00-repository-manifest.md`, filesystem scans |
| `01-evidence-registry.json` | `docs/catalogs/*.json`, `storage/app/entity-catalog.json` (local) |
| `docs/catalogs/authorization-matrix.json` | 257 `ModuleResource` classes |
| `docs/catalogs/api-routes-catalog.json` | `php artisan route:list --json` |

---

## 4. Evidence References

### 4.1 Stage 03 evidence IDs

| Evidence ID | Command / path | Result | Confidence |
|-------------|----------------|--------|------------|
| EV-S03-001 | `git ls-files 'app/**/*.php' \| wc -l` | 318 | VERIFIED |
| EV-S03-002 | `ls Modules/ \| wc -l` | 45 | VERIFIED |
| EV-S03-003 | `git ls-files 'bootstrap/*.php'` | 5 files | VERIFIED |
| EV-S03-004 | `git ls-files 'config/*.php' \| wc -l` | 24 | VERIFIED |
| EV-S03-005 | `git ls-files 'database/migrations/*.php' 'Modules/*/database/migrations/*.php' \| wc -l` | 244 | VERIFIED |
| EV-S03-006 | `git ls-files 'routes/*.php' \| wc -l` | 3 | VERIFIED |
| EV-S03-007 | `test -f public/index.php` | exists | VERIFIED |
| EV-S03-008 | `git ls-files 'resources/**/*' \| head` | css, js, views | VERIFIED |
| EV-S03-009 | `git ls-files 'tests/**/*.php' \| wc -l` | 173 | VERIFIED |
| EV-S03-010 | `git ls-files scripts/ \| wc -l` | 21 | VERIFIED |
| EV-S03-011 | `git ls-files 'docs/catalogs/*.json'` | 2 JSON catalogs | VERIFIED |
| EV-S03-012 | `ls mobile/` | `capacitor.config.json` | VERIFIED |
| EV-S03-013 | `storage/app/.gitignore` | local data excluded | VERIFIED |
| EV-S03-014 | `test -d vendor` + `.gitignore` | excluded | VERIFIED |
| EV-S03-015 | `test -d node_modules` + `.gitignore` | excluded | VERIFIED |
| EV-S03-016 | `git ls-files 'Modules/*/module.json' \| wc -l` | 45 | VERIFIED |
| EV-S03-017 | `git ls-files 'Modules/*/composer.json' \| wc -l` | 44 | VERIFIED |
| EV-S03-018 | Module scan | Exam lacks `composer.json` | VERIFIED |
| EV-S03-019 | `modules_statuses.json` | 45 keys, all `true` | VERIFIED |
| EV-S03-020 | `public/index.php` → `bootstrap/app.php` | HTTP bootstrap chain | VERIFIED |
| EV-S03-021 | `bootstrap/app.php:17-23` + module api routes | API routing | VERIFIED |
| EV-S03-022 | `bootstrap/app.php:23` | `health: '/up'` | VERIFIED |
| EV-S03-023 | `test -f artisan` | CLI entry | VERIFIED |
| EV-S03-024 | `AdminPanelProvider.php` `->id('admin')->path('admin')` | admin panel | VERIFIED |
| EV-S03-025 | `PlatformPanelProvider.php` `->id('platform')` | platform panel | VERIFIED |
| EV-S03-026 | `ParentPanelProvider.php` `->id('parent')` | parent panel | VERIFIED |
| EV-S03-027 | `composer.json` scripts.dev | queue in dev stack | VERIFIED |
| EV-S03-028 | `grep -c 'Schedule::command' routes/console.php` | 28 | VERIFIED |
| EV-S03-029 | `vite.config.js` inputs | 4 entry files | VERIFIED |
| EV-S03-030 | `mobile/capacitor.config.json` | Capacitor webDir | VERIFIED |
| EV-S03-031 | `find Modules -name '*Resource.php' -exec grep -l extends ModuleResource {} + \| wc -l` | 257 | VERIFIED |
| EV-S03-032 | `find Modules -path '*/routes/api.php' \| wc -l` | 14 | VERIFIED |
| EV-S03-033 | `find Modules -path '*/routes/web.php' \| wc -l` | 44 | VERIFIED |
| EV-S03-034 | `php artisan list --raw \| wc -l` | 329 | VERIFIED |
| EV-S03-035 | `bootstrap/providers.php` | 4 providers | VERIFIED |
| EV-S03-036 | `config/filament-modules.php` | mode BOTH, auto-register | VERIFIED |
| EV-S03-037 | `php artisan route:list --except-vendor --json \| jq length` | 1778 | VERIFIED |
| EV-S03-038 | `docs/catalogs/api-routes-catalog.json` `route_count` | 101 | VERIFIED |

### 4.2 Cross-reference to prior REAP evidence

| Prior ID | Reused finding | Stage |
|----------|----------------|-------|
| EV-M01-014 | 45 modules | Stage 01 |
| EV-00002 | 257 ModuleResource classes | REAP_AUDIT |
| EV-00003 | 1778 routes | REAP_AUDIT |
| EV-00004 | 101 API routes | REAP_AUDIT |
| EV-00026 | 3 Filament panels only | REAP_AUDIT |

### 4.3 Link to Evidence Registry (Stage 02)

`01-evidence-registry.json` indexes domain evidence (entities, API, auth, workflows). This stage catalog provides the **structural index** those entries hang from:

- `api` group (101) → routes resolved via `routes/api.php` + module `RouteServiceProvider`
- `authorization` group (281) → maps to `ModuleResource` classes per module
- `entity` group (439) → maps to `Modules/*/app/Models/` + migrations

---

## 5. Coverage Analysis

| Structural dimension | Cataloged? | Depth | Notes |
|---------------------|------------|-------|-------|
| Root folders | Yes | All top-level dirs | Agent/IDE dirs noted, not expanded |
| Composer packages | Yes | `require` + key dev | Not full `composer.lock` tree |
| NPM / Vite | Yes | Scripts + inputs | No per-chunk bundle analysis |
| PSR-4 namespaces | Yes | All 45 module prefixes | |
| Module manifests | Yes | 45 `module.json` | Provider class per module |
| Module internal layout | Yes | Canonical + tier variance | |
| HTTP entrypoints | Yes | web, api, health, webhooks | |
| CLI entrypoints | Yes | artisan, console schedule | Command taxonomy not expanded |
| Filament panels | Yes | 3 panels | No per-page inventory |
| Queue / jobs | Partial | Known paths only | Not all job classes listed |
| Mobile shell | Yes | Capacitor config | No native iOS/Android projects |
| Test layout | Partial | Count only | Per-module test dirs not enumerated |
| `vendor/` internals | **No** | Excluded | By design |
| Business workflows | **No** | Out of scope | Stage 03 boundary |

**Structural coverage score (self-assessment):** root + modules + entrypoints + namespaces = **complete** for Stage 03 gate. Per-class inventories deferred to Stage 04+.

---

## 6. Gap Analysis

| Gap ID | Description | Severity | Confidence | Mitigation |
|--------|-------------|----------|------------|------------|
| GAP-S03-01 | `Exam` module missing `composer.json` (44/45) | Low | VERIFIED | Align with other modules or document intentional omission |
| GAP-S03-02 | Six **lean** modules lack full folder set (config/tests/resources) | Medium | VERIFIED | Stage 04 module deep-dive per tier |
| GAP-S03-03 | `Procurement`, `Global`, `Exam` have 0 `ModuleResource` — UI may live elsewhere | Medium | VERIFIED | Trace Filament pages / custom routes in Stage 04 |
| GAP-S03-04 | Module `api.php` (14) vs total API routes (101) — majority in central `routes/api.php` | Low | VERIFIED | Documented in Dependency Notes |
| GAP-S03-05 | `storage/app/entity-catalog.json` not git-tracked | Medium | VERIFIED | Commit to `docs/catalogs/` or regenerate in CI |
| GAP-S03-06 | Informal domain clustering is **INFERRED** from names only | Low | INFERRED | Do not use as authoritative domain model |
| GAP-S03-07 | Inter-module PHP `use` graph not generated | Medium | UNVERIFIED | Optional static analysis in Stage 04 |
| GAP-S03-08 | `mobile/` has no native project scaffolding in repo | Low | VERIFIED | Capacitor wrapper only; app is web-first |

---

## 7. Validation Checklist

### 7.1 Coverage

- [x] Root folders identified and roles assigned
- [x] Composer and NPM packages documented
- [x] PSR-4 namespaces mapped (`App\`, `Modules\{Name}\`)
- [x] All 45 modules listed with provider and namespace
- [x] HTTP, CLI, Filament, Vite, queue entrypoints recorded
- [x] Canonical module tree documented
- [x] Non-module candidates (`mobile/`, `scripts/`, `docs/`) distinguished
- [x] No business logic or workflow semantics included

### 7.2 Missing Evidence

- [ ] Full `composer.lock` dependency tree — **deferred** (vendor excluded)
- [ ] Per-module `use` import graph — **deferred** to Stage 04
- [ ] Filament page/widget inventory per panel — **deferred**
- [ ] Native mobile build artifacts — **not present** in repo

### 7.3 Ambiguous Findings

- [ ] **AF-S03-01:** "Lean" vs "Full" tier is a **structural** heuristic (folder presence), not maturity or production readiness. **Confidence:** INFERRED.
- [ ] **AF-S03-02:** `Event` module uses `EventModuleServiceProvider` (naming exception). **Confidence:** VERIFIED.
- [ ] **AF-S03-03:** `composer.json` product name vs `laravel/laravel` package name — both recorded, not merged (inherits SA-05 from Stage 01).

### 7.4 Risk Assessment

| Risk | Severity | Mitigation |
|------|----------|------------|
| 45 modules × uneven structure complicates onboarding | Medium | Tier labels + canonical tree in this catalog |
| Central `routes/api.php` hides module ownership | Low | Cross-ref `api-routes-catalog.json` |
| Local-only entity catalog breaks reproducibility | Medium | GAP-S03-05 — commit or CI-generate |
| 257 Filament resources inflate permission surface | Medium | Stage 02 `authorization` evidence group |

---

## 8. Stage Gate

| Criterion | Status |
|-----------|--------|
| Artifact `02-structure-catalog.md` produced | **Done** |
| Folders, packages, namespaces, modules, entrypoints identified | **Done** |
| Module Candidates table (45 + non-module) | **Done** |
| Dependency Notes | **Done** |
| Evidence References (EV-S03-001 … EV-S03-038) | **Done** |
| Coverage Analysis | **Done** |
| Gap Analysis | **Done** |
| Validation Checklist | **Done** |
| Business analysis absent | **Confirmed** |
| Ready for REAP Stage 04 | **Yes** (conditional on checklist acceptance) |

---

## 9. Regenerate Structure Scans

```bash
git rev-parse --short HEAD
ls Modules/ | sort
for m in Modules/*/; do
  name=$(basename "$m")
  subdirs=$(find "$m" -maxdepth 1 -type d ! -path "$m" -printf '%f\n' | sort | paste -sd,)
  prov=$(php -r '$j=json_decode(file_get_contents("'$m'module.json"),true); echo $j["providers"][0]??"";')
  echo "$name|$prov|$subdirs"
done
find Modules -name '*Resource.php' -exec grep -l 'extends ModuleResource' {} + | wc -l
find Modules -path '*/routes/api.php' | wc -l
find Modules -path '*/routes/web.php' | wc -l
php artisan route:list --except-vendor --json | php -r 'echo count(json_decode(stream_get_contents(STDIN),true));'
```

---

*REAP Stage 03 — Structure Discovery. Evidence-first. No business logic.*
