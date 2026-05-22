# FoundationOS — AI Assistant Guide

## What This Application Is

FoundationOS is a modular SaaS ERP for educational institutions built on Laravel 13, Filament v5, and Livewire v4. It uses shared-database multi-tenancy (no `stancl/tenancy`) where one application instance serves many tenants with clear domain boundaries enforced by `tenant_id` on every operational table.

The primary admin panel lives at `/admin` (panel ID: `admin`). The tenant model is `Modules\Core\Models\Tenant`.

---

## Directory Layout

```
app/                        Core Laravel application layer
  Console/Commands/         Artisan commands (Moodle sync, workflow ops, library)
  Filament/Imports/         ~100 Filament importers (one per domain model)
  Integrations/Moodle/      Moodle API client, mapper, outbox service
  Jobs/                     Queue jobs (ProcessMoodleSyncOutboxJob)
  Models/                   app/Models/User is a thin bridge → Modules\Core\Models\User
  Observers/                Model observers (UserObserver, CourseObserver, etc.)
  Policies/                 app-level policies
  Providers/Filament/       AdminPanelProvider — single panel configuration

Modules/                    Feature modules (coolsam/modules v5, nwidart compatible)
  Core/                     Tenancy, users, organizations, subscription plans
  Global/                   Countries, provinces, cities, districts, timezones
  School/                   K-12: curricula, classes, students, attendance, grades
  Campus/                   Higher-ed: faculties, study programs, lecturers, theses
  Enrollment/               Admissions, applicants, registrations, exams
  Employee/                 HR: positions, contracts, payroll, KPI, leave
  Finance/                  Chart of accounts, student invoices, payments, journals
  Procurement/              Requisitions, RFQs, purchase orders, vendor bills
  Library/                  Books, loans, fines, SLIMS import integration
  Monitoring/               Audit logs, file uploads
  Workflow/                 Metadata-driven approval engine (V2)

config/                     Standard Laravel + filament-shield, filament-modules, permission, moodle
database/
  migrations/               Core (users, jobs, cache) + Filament import/export tables
  seeders/                  DatabaseSeeder → MvpDemoSeeder
scripts/                    One-off PHP scripts (Moodle bootstrap, SLIMS import helpers)
tests/
  Feature/                  CoreTenancyFoundationTest, WorkflowTests, LibraryTest, etc.
  Unit/                     ExampleTest
```

Each module under `Modules/<Name>/` follows this internal structure:

```
app/
  Filament/
    Pages/          Filament custom pages
    Resources/      Resource directories (one dir per resource)
      <Model>/
        Pages/      List/Create/Edit/View pages
        Schemas/    Form and Infolist schema classes
        Tables/     Table definition class
        RelationManagers/
        <Model>Resource.php
    Widgets/        Stats overview widgets
    Support/        Shared Filament helpers (ImportTableActions, ModuleResource, TenantField)
  Models/
  Policies/
  Providers/        <Name>ServiceProvider, EventServiceProvider, RouteServiceProvider
  Services/         Domain service classes
  Contracts/        Interfaces (especially in Workflow)
  Enums/
  Events/
  Exceptions/
  Observers/
database/
  migrations/
  seeders/
  factories/
resources/views/
routes/
  web.php
  api.php
```

---

## Key Architectural Decisions

### User Model
`app/Models/User` extends `Modules\Core\Models\User` — it is a bridge for ecosystem compatibility only. All business logic lives in the Core module User. Always use `Modules\Core\Models\User` in module code.

### Multi-Tenancy Pattern
- `Tenant` is the SaaS account boundary.
- `User` is global; membership to a tenant is via `user_tenant_roles`.
- Every operational model carries `tenant_id`; many also carry `organization_id`.
- Filament panel uses `->tenant(Modules\Core\Models\Tenant::class)`.
- Spatie Permission runs in **teams mode** with `team_foreign_key = tenant_id`.

### Filament Resource Pattern
All module resources extend `Modules\Core\Filament\Support\ModuleResource` (not the base `Resource`). This base class:
- Handles tenant scoping safety checks
- Pulls navigation group/icon/label from `FilamentUi`
- Excludes `SoftDeletingScope` from queries
- Prevents mutations on global resources by non-super-admins

Form, infolist, and table definitions are split into dedicated `Schemas/` and `Tables/` classes — do not put them inline in the resource class.

### Labels and Bilingual UI
All display labels come from `Modules\Core\Support\FilamentUi`. The app is bilingual (`id` / `en`). Never hardcode Indonesian or English labels directly in resource/form code — call `FilamentUi::resource()`, `FilamentUi::text()`, or `FilamentUi::module()`.

### Import / Export
Every resource has an `ImportAction` and CSV template button via `Modules\Core\Filament\Support\ImportTableActions`. The base importer is `app/Filament/Imports/BaseModelImporter`.

---

## Module Details

| Module | Domain | Key Models |
|--------|--------|------------|
| Core | Tenancy, users, orgs | Tenant, Organization, User, TenantRole, AcademicYear, Department |
| Global | Reference data | Country, Province, City, District, Village, Timezone |
| School | K-12 education | Curriculum, Subject, SchoolClass, Teacher, Student, Attendance, Assessment |
| Campus | Higher education | Faculty, StudyProgram, Course, Lecturer, CollageStudent, StudyPlan, Thesis |
| Enrollment | Admissions | AdmissionPeriod, Applicant, Registration, ExamSchedule |
| Employee | HR | Position, Employee, EmploymentContract, SalarySlip, KpiTemplate, LeaveRequest |
| Finance | Accounting | ChartOfAccount, Budget, StudentInvoice, Payment, JournalEntry |
| Procurement | Purchasing | PurchaseRequisition, RFQ, PurchaseOrder, GoodsReceipt, VendorBill |
| Library | Library mgmt | Book, BookCopy, Member, Loan, Fine, LibrarySerial |
| Monitoring | Audit | AuditLog, FileUpload |
| Workflow | Approvals | Workflow, WorkflowStep, WorkflowInstance, WorkflowAssignment |

---

## Workflow V2 (Metadata-Driven)

The Workflow module is the most complex domain. Key components:

- `WorkflowResolver` — finds the best-matching workflow for a tenant/organization
- `WorkflowInstanceStarter` — snapshots a workflow definition and creates an instance
- `WorkflowEngine` — advances, returns, and cancels instances
- `RuleEngine` — evaluates JSONLogic rules for transition conditions
- `WorkflowAutomatedActionRunner` — runs database-driven automation hooks

Workflows are `tenant-first`: `tenant_id` required, `organization_id` optional (null = tenant-wide). The resolver picks the most specific match (tenant+org > tenant-wide).

**Artisan commands:**
```bash
php artisan fos:workflow:health-check --tenant=1
php artisan fos:workflow:retry-sla --tenant=1
php artisan fos:workflow:retry-automation {id} {status}
```

**Setup pilot workflows:**
```bash
# Procurement approval
php artisan fos:workflow:setup-procurement-pilot 1 --manager=10 --finance=11 --executive=12

# Budget approval
php artisan fos:workflow:setup-budget-workflow 1 --organization=5 --finance=11 --executive=12
```

---

## Moodle Integration

FOS → Moodle (one-way master data sync) via an outbox pattern:

- `MoodleOutboxService` queues sync events into `moodle_sync_outbox`
- `ProcessMoodleSyncOutboxJob` drains the outbox
- `MoodleClient` wraps the Moodle REST API
- `MoodleMapper` translates FOS entities to Moodle payloads

Logical partitioning strategy:
- Tenant → Moodle Course Category (`idnumber = fos_tenant_{id}`)
- Course → Moodle Course (`idnumber = fos_course_{id}`)
- User → Moodle User (`idnumber = fos_user_{id}`)

Model observers (`CourseObserver`, `StudentObserver`, `ClassStudentObserver`) trigger sync events automatically.

**Artisan commands:**
```bash
php artisan moodle:sync-cohorts
php artisan moodle:pull-grades
php artisan moodle:pull-attendance
php artisan moodle:health-check
php artisan moodle:reconcile
```

---

## Filament Shield (Authorization)

Shield + Spatie Permission is the **single source of truth** for panel authorization.

After adding new resources/pages, regenerate permissions:
```bash
php artisan shield:generate --all --panel=admin --option=permissions --no-interaction
php artisan optimize:clear
```

Assign super admin per tenant:
```bash
php artisan shield:super-admin --user=1 --tenant=1 --panel=admin
```

Important: `TenantRole`/`UserTenantRole` in Core module = domain membership layer. Spatie Permission = panel authorization layer. Both must be correct for the panel to work.

---

## Common Development Commands

```bash
# Start dev environment (server + queue + logs + vite)
composer run dev

# Run all tests
php artisan test --compact

# Run specific test
php artisan test --compact tests/Feature/CoreTenancyFoundationTest.php
php artisan test --compact --filter=testName

# Format PHP after changes
vendor/bin/pint --dirty --format agent

# Clear all caches (required after adding module resources)
php artisan optimize:clear

# List routes
php artisan route:list --except-vendor

# Create super admin user
php artisan make:super-admin
```

---

## Translasi & Label

Aplikasi ini bilingual (`id` / `en`). Semua teks UI **wajib** melewati `FilamentUi` — jangan pernah hardcode label berbahasa Inggris atau Indonesia langsung di kode.

### Aturan wajib

1. **Field label** (form field, table column, infolist entry) → `->label(FilamentUi::field('field_name'))`.
   - `field_name` adalah nama kolom dalam format `snake_case`.
   - ✅ `TextInput::make('phone_number')->label(FilamentUi::field('phone_number'))`
   - ❌ `TextInput::make('phone_number')->label('Phone Number')`

2. **Section / Tab / Fieldset / Wizard step title** → `Section::make(FilamentUi::text('Title'))`.
   - ✅ `Section::make(FilamentUi::text('General information'))`
   - ❌ `Section::make('General Information')`

3. **Placeholder, helperText, prefix, suffix** → `FilamentUi::text(...)` jika ada teks naratif.
   - ✅ `->placeholder(FilamentUi::text('Enter phone number'))`
   - ❌ `->placeholder('Enter phone number')`

4. **Action label** custom → `Action::make('name')->label(FilamentUi::text('Action Name'))`.
   - Action bawaan Filament (Create/Edit/Delete) sudah ditranslasi Filament — tidak perlu diubah.

5. **Modal heading / description** → wajib lewat `FilamentUi::text(...)`.

6. **Resource navigation** sudah otomatis via `ModuleResource::getNavigationLabel()` dan `getModelLabel()` — tidak perlu override.

### Frasa & kata baru

Tambahkan ke `PHRASES` atau `WORDS` di `Modules/Core/app/Support/FilamentUi.php` sebelum menggunakan `FilamentUi::text('...')` dengan frasa baru. Pastikan ada entry untuk singular dan plural.

### Linter

```bash
# Deteksi anti-pattern secara lokal
php scripts/lint-translations.php

# Lewat Composer
composer run lint:translations
```

Suppress per baris: tambahkan `// fos:lint-ignore-translation` di akhir baris yang sengaja hardcode (mis. nilai teknis seperti FQCN model).

CI memblokir PR yang memperkenalkan hardcoded label. Setelah deploy, wajib clear cache agar navigation group locale baru efektif:

```bash
php artisan optimize:clear
```

---

## Adding a New Module Resource

1. Use `php artisan make:filament-resource --help` to find options; always pass `--no-interaction`.
2. Place the resource inside `Modules/<Name>/app/Filament/Resources/<Models>/`.
3. Extend `Modules\Core\Filament\Support\ModuleResource`, not `Filament\Resources\Resource`.
4. Split form into `Schemas/<Model>Form.php`, infolist into `Schemas/<Model>Infolist.php`, table into `Tables/<Models>Table.php`.
5. Add the resource to the `navigationSortMap()` in `ModuleResource.php`.
6. Add an importer to `app/Filament/Imports/` extending `BaseModelImporter`.
7. Run `php artisan optimize:clear` and `php artisan shield:generate --all --panel=admin --option=permissions --no-interaction`.
8. Write a feature test using `RefreshDatabase` and the module model factories.
9. **Semua label, section title, placeholder, dan helperText wajib melalui `FilamentUi`** (lihat section "Translasi & Label" di atas).

---

## Testing Conventions

- All tests are PHPUnit classes (no Pest). Use `php artisan make:test --phpunit {Name}`.
- Feature tests use `RefreshDatabase` and `Modules\Core\Models\*` factories.
- Always authenticate (`actingAs`) before testing Filament panel functionality.
- Use `Livewire::test()` for Filament resource/page tests.
- Reference test files: `tests/Feature/CoreTenancyFoundationTest.php`, `tests/Feature/WorkflowDefinitionLifecycleTest.php`.

---

## Reference Documentation

| Topic | File |
|-------|------|
| Application overview | `README.md` |
| Development roadmap (Workflow V3, Procurement automation, Moodle reconcile, Tenancy hardening, Public API, Moodle campus) | `ROADMAP.md` |
| Moodle setup | `MOODLE.md`, `MOODLE_HARDENING_CHECKLIST.md` |
| Workflow details | `WORKFLOW.md` |
| Procurement runbook | `PROCUREMENT.md` |
| Finance golden path | `FINANCE.md` |
| Library integration | `LIBRARY.md` |

---

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- filament/filament (FILAMENT) - v5
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd at `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs. Never run commands to serve the site. It is always available.
- Use the `herd` CLI to manage services, PHP versions, and sites (e.g. `herd sites`, `herd services:start <service>`, `herd php:list`). Run `herd list` to discover all available commands.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

=== filament/filament rules ===

## Filament

- Filament is used by this application. Follow the existing conventions for how and where it is implemented.
- Filament is a Server-Driven UI (SDUI) framework for Laravel that lets you define user interfaces in PHP using structured configuration objects. Built on Livewire, Alpine.js, and Tailwind CSS.
- Use the `search-docs` tool for official documentation on Artisan commands, code examples, testing, relationships, and idiomatic practices. If `search-docs` is unavailable, refer to https://filamentphp.com/docs.

### Artisan

- Always use Filament-specific Artisan commands to create files. Find available commands with the `list-artisan-commands` tool, or run `php artisan --help`.
- Always inspect required options before running a command, and always pass `--no-interaction`.

### Patterns

Always use static `make()` methods to initialize components. Most configuration methods accept a `Closure` for dynamic values.

Use `Get $get` to read other form field values for conditional logic:

<code-snippet name="Conditional form field visibility" lang="php">
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

Select::make('type')
    ->options(CompanyType::class)
    ->required()
    ->live(),

TextInput::make('company_name')
    ->required()
    ->visible(fn (Get $get): bool => $get('type') === 'business'),

</code-snippet>

Use `state()` with a `Closure` to compute derived column values:

<code-snippet name="Computed table column value" lang="php">
use Filament\Tables\Columns\TextColumn;

TextColumn::make('full_name')
    ->state(fn (User $record): string => "{$record->first_name} {$record->last_name}"),

</code-snippet>

Actions encapsulate a button with an optional modal form and logic:

<code-snippet name="Action with modal form" lang="php">
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;

Action::make('updateEmail')
    ->schema([
        TextInput::make('email')
            ->email()
            ->required(),
    ])
    ->action(fn (array $data, User $record) => $record->update($data))

</code-snippet>

### Testing

Always authenticate before testing panel functionality. Filament uses Livewire, so use `Livewire::test()` or `livewire()` (available when `pestphp/pest-plugin-livewire` is in `composer.json`):

<code-snippet name="Table test" lang="php">
use function Pest\Livewire\livewire;

livewire(ListUsers::class)
    ->assertCanSeeTableRecords($users)
    ->searchTable($users->first()->name)
    ->assertCanSeeTableRecords($users->take(1))
    ->assertCanNotSeeTableRecords($users->skip(1));

</code-snippet>

<code-snippet name="Create resource test" lang="php">
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

livewire(CreateUser::class)
    ->fillForm([
        'name' => 'Test',
        'email' => 'test@example.com',
    ])
    ->call('create')
    ->assertNotified()
    ->assertRedirect();

assertDatabaseHas(User::class, [
    'name' => 'Test',
    'email' => 'test@example.com',
]);

</code-snippet>

<code-snippet name="Testing validation" lang="php">
use function Pest\Livewire\livewire;

livewire(CreateUser::class)
    ->fillForm([
        'name' => null,
        'email' => 'invalid-email',
    ])
    ->call('create')
    ->assertHasFormErrors([
        'name' => 'required',
        'email' => 'email',
    ])
    ->assertNotNotified();

</code-snippet>

<code-snippet name="Calling actions in pages" lang="php">
use Filament\Actions\DeleteAction;
use function Pest\Livewire\livewire;

livewire(EditUser::class, ['record' => $user->id])
    ->callAction(DeleteAction::class)
    ->assertNotified()
    ->assertRedirect();

</code-snippet>

<code-snippet name="Calling actions in tables" lang="php">
use Filament\Actions\Testing\TestAction;
use function Pest\Livewire\livewire;

livewire(ListUsers::class)
    ->callAction(TestAction::make('promote')->table($user), [
        'role' => 'admin',
    ])
    ->assertNotified();

</code-snippet>

### Correct Namespaces

- Form fields (`TextInput`, `Select`, etc.): `Filament\Forms\Components\`
- Infolist entries (`TextEntry`, `IconEntry`, etc.): `Filament\Infolists\Components\`
- Layout components (`Grid`, `Section`, `Fieldset`, `Tabs`, `Wizard`, etc.): `Filament\Schemas\Components\`
- Schema utilities (`Get`, `Set`, etc.): `Filament\Schemas\Components\Utilities\`
- Actions (`DeleteAction`, `CreateAction`, etc.): `Filament\Actions\`. Never use `Filament\Tables\Actions\`, `Filament\Forms\Actions\`, or any other sub-namespace for actions.
- Icons: `Filament\Support\Icons\Heroicon` enum (e.g., `Heroicon::PencilSquare`)

### Common Mistakes

- **Never assume public file visibility.** File visibility is `private` by default. Always use `->visibility('public')` when public access is needed.
- **Never assume full-width layout.** `Grid`, `Section`, and `Fieldset` do not span all columns by default. Explicitly set column spans when needed.

</laravel-boost-guidelines>
