<?php

/**
 * Scaffold ROADMAP v04 modules: providers, migrations, models, minimal Filament resources.
 *
 * Usage: php scripts/scaffold-roadmap-v04.php
 */

declare(strict_types=1);

require dirname(__DIR__).'/vendor/autoload.php';

use Illuminate\Support\Str;

$basePath = dirname(__DIR__);

$modules = [
    'Legal' => [
        'tables' => ['legal_documents', 'contracts', 'contract_parties', 'contract_attachments'],
        'primary' => 'LegalDocument',
    ],
    'Asset' => [
        'tables' => ['asset_categories', 'assets', 'asset_movements', 'asset_maintenances', 'asset_depreciations', 'asset_insurances', 'asset_loans'],
        'primary' => 'Asset',
    ],
    'Dms' => [
        'tables' => ['document_folders', 'documents', 'document_versions', 'document_access_logs'],
        'primary' => 'Document',
    ],
    'Helpdesk' => [
        'tables' => ['ticket_categories', 'tickets', 'ticket_comments', 'ticket_attachments', 'knowledge_base_articles', 'ticket_satisfactions'],
        'primary' => 'Ticket',
    ],
    'Facility' => [
        'tables' => ['buildings', 'floors', 'rooms', 'room_equipment', 'room_bookings', 'room_maintenance_logs', 'utility_readings'],
        'primary' => 'Room',
    ],
    'EOffice' => [
        'tables' => ['letter_categories', 'letters', 'letter_attachments', 'letter_dispositions', 'letter_templates'],
        'primary' => 'Letter',
    ],
    'ItOps' => [
        'tables' => ['software_licenses', 'user_accounts', 'backup_jobs', 'ip_address_records'],
        'primary' => 'SoftwareLicense',
    ],
    'Transport' => [
        'tables' => ['vehicles', 'drivers', 'routes', 'route_stops', 'route_schedules', 'student_shuttle_subscriptions', 'boarding_logs', 'vehicle_maintenances', 'vehicle_operating_costs'],
        'primary' => 'Vehicle',
    ],
    'Boarding' => [
        'tables' => ['dormitories', 'room_assignments', 'boarding_attendances', 'boarding_leave_permits', 'room_inspections', 'boarding_meal_records', 'laundry_records'],
        'primary' => 'Dormitory',
    ],
    'Cafeteria' => [
        'tables' => ['cafeteria_tenants', 'menus', 'menu_stocks', 'cafeteria_transactions', 'meal_subscriptions', 'meal_ratings', 'student_wallets', 'cafeteria_inspections'],
        'primary' => 'Menu',
    ],
    'PhysicalSecurity' => [
        'tables' => ['visitors', 'visitor_logs', 'guards', 'patrol_schedules', 'patrol_logs', 'security_incidents', 'emergency_alerts', 'safety_checklists'],
        'primary' => 'Visitor',
    ],
    'Counseling' => [
        'tables' => ['counselors', 'counseling_cases', 'counseling_sessions', 'counseling_notes', 'case_follow_ups', 'anonymous_reports', 'wellbeing_surveys'],
        'primary' => 'CounselingCase',
    ],
    'Clinic' => [
        'tables' => ['health_records', 'allergies', 'medical_histories', 'clinic_visits', 'medication_stocks', 'vaccination_records', 'injury_reports', 'medical_consents', 'medical_referrals'],
        'primary' => 'ClinicVisit',
    ],
    'Event' => [
        'tables' => ['events', 'event_proposals', 'event_budgets', 'event_committees', 'event_participants', 'event_tickets', 'event_check_ins', 'event_certificates', 'event_sponsors', 'event_vendors'],
        'primary' => 'Event',
    ],
    'MerchOrder' => [
        'tables' => ['uniform_packages', 'book_packages', 'merch_orders', 'merch_order_items', 'merch_pickups', 'merch_returns'],
        'primary' => 'MerchOrder',
    ],
    'Alumni' => [
        'tables' => ['alumni', 'alumnus_employments', 'alumnus_educations', 'alumnus_achievements', 'job_postings', 'internship_postings', 'company_partners', 'mentoring_sessions', 'alumni_donations'],
        'primary' => 'Alumnus',
    ],
    'Cms' => [
        'tables' => ['sites', 'pages', 'page_blocks', 'articles', 'article_categories', 'banners', 'testimonials', 'galleries', 'cms_media', 'menu_items'],
        'primary' => 'Site',
    ],
    'Donation' => [
        'tables' => ['campaigns', 'campaign_updates', 'donors', 'donations', 'recurring_donations', 'endowments', 'wakafs'],
        'primary' => 'Campaign',
    ],
    'Training' => [
        'tables' => ['training_programs', 'training_batches', 'training_sessions', 'instructors', 'training_enrollments', 'training_payments', 'training_certificates', 'corporate_training_packages'],
        'primary' => 'TrainingProgram',
    ],
    'Risk' => [
        'tables' => ['risk_categories', 'risks', 'risk_assessments', 'risk_treatments', 'risk_owners', 'key_risk_indicators', 'risk_incidents'],
        'primary' => 'Risk',
    ],
    'InternalAudit' => [
        'tables' => ['audit_programs', 'audit_plans', 'audit_engagements', 'audit_checklist_templates', 'audit_checklist_items', 'audit_findings', 'finding_evidences', 'corrective_actions', 'preventive_actions'],
        'primary' => 'AuditEngagement',
    ],
    'IsoCompliance' => [
        'tables' => ['information_assets', 'iso_controls', 'control_implementations', 'statements_of_applicability', 'iso_evidences', 'iso_policies', 'iso_incidents'],
        'primary' => 'IsoControl',
    ],
    'EducationQa' => [
        'tables' => ['quality_standards', 'quality_indicators', 'quality_surveys', 'survey_responses', 'accreditation_cycles', 'accreditation_documents', 'gap_analyses', 'improvement_plans'],
        'primary' => 'QualityStandard',
    ],
    'KpiEnterprise' => [
        'tables' => ['kpi_areas', 'kpi_metrics', 'kpi_targets', 'kpi_actuals', 'kpi_weights', 'kpi_cascades'],
        'primary' => 'KpiMetric',
    ],
    'Capacity' => [
        'tables' => ['capacity_resources', 'capacity_utilizations', 'capacity_forecasts'],
        'primary' => 'CapacityResource',
    ],
    'Messaging' => [
        'tables' => ['notification_templates', 'notification_preferences', 'notification_deliveries'],
        'primary' => 'NotificationTemplate',
    ],
];

function studly(string $value): string
{
    return Str::studly($value);
}

function snake(string $value): string
{
    return Str::snake($value);
}

function writeFile(string $path, string $content): void
{
    $dir = dirname($path);
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($path, $content);
    echo "  wrote {$path}\n";
}

function serviceProviderContent(string $module): string
{
    $lower = strtolower($module);

    return <<<PHP
<?php

namespace Modules\\{$module}\\Providers;

use Nwidart\\Modules\\Support\\ModuleServiceProvider;

class {$module}ServiceProvider extends ModuleServiceProvider
{
    protected string \$name = '{$module}';

    protected string \$nameLower = '{$lower}';

    protected array \$providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}

PHP;
}

function routeProviderContent(string $module): string
{
    return <<<PHP
<?php

namespace Modules\\{$module}\\Providers;

use Illuminate\\Foundation\\Support\\Providers\\RouteServiceProvider as ServiceProvider;
use Illuminate\\Support\\Facades\\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        \$this->routes(function (): void {
            Route::middleware('web')->group(module_path('{$module}', 'routes/web.php'));
        });
    }
}

PHP;
}

function eventProviderContent(string $module): string
{
    return <<<PHP
<?php

namespace Modules\\{$module}\\Providers;

use Illuminate\\Foundation\\Support\\Providers\\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected \$listen = [];
}

PHP;
}

function migrationContent(string $module, array $tables): string
{
    $creates = '';
    foreach ($tables as $table) {
        $singular = Str::singular($table);
        $creates .= <<<BLADE

        Schema::create('{$table}', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            \$table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            \$table->string('code')->nullable();
            \$table->string('name')->nullable();
            \$table->string('status')->default('active');
            \$table->text('description')->nullable();
            \$table->json('meta')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });

BLADE;
    }

    $drops = implode("\n        ", array_map(fn ($t) => "Schema::dropIfExists('{$t}');", array_reverse($tables)));

    return <<<PHP
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
{$creates}
    }

    public function down(): void
    {
        {$drops}
    }
};

PHP;
}

function modelContent(string $module, string $model, string $table): string
{
    return <<<PHP
<?php

namespace Modules\\{$module}\\Models;

use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;
use Illuminate\\Database\\Eloquent\\SoftDeletes;
use Modules\\Core\\Models\\Concerns\\BelongsToTenant;
use Modules\\Core\\Models\\Organization;
use Modules\\Monitoring\\Models\\Concerns\\HasAuditTrail;

class {$model} extends Model
{
    use BelongsToTenant, HasAuditTrail, HasFactory, SoftDeletes;

    protected \$table = '{$table}';

    protected \$fillable = [
        'tenant_id',
        'organization_id',
        'code',
        'name',
        'status',
        'description',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return \$this->belongsTo(Organization::class);
    }
}

PHP;
}

function listPageContent(string $module, string $model): string
{
    $resource = "{$model}Resource";
    $plural = Str::plural($model);
    $var = lcfirst($plural);

    return <<<PHP
<?php

namespace Modules\\{$module}\\Filament\\Resources\\{$plural}\\Pages;

use Filament\\Resources\\Pages\\ListRecords;
use Modules\\{$module}\\Filament\\Resources\\{$plural}\\{$resource};

class List{$plural} extends ListRecords
{
    protected static string \$resource = {$resource}::class;
}

PHP;
}

function createPageContent(string $module, string $model): string
{
    $resource = "{$model}Resource";
    $plural = Str::plural($model);

    return <<<PHP
<?php

namespace Modules\\{$module}\\Filament\\Resources\\{$plural}\\Pages;

use Filament\\Resources\\Pages\\CreateRecord;
use Modules\\{$module}\\Filament\\Resources\\{$plural}\\{$resource};

class Create{$model} extends CreateRecord
{
    protected static string \$resource = {$resource}::class;
}

PHP;
}

function editPageContent(string $module, string $model): string
{
    $resource = "{$model}Resource";
    $plural = Str::plural($model);

    return <<<PHP
<?php

namespace Modules\\{$module}\\Filament\\Resources\\{$plural}\\Pages;

use Filament\\Resources\\Pages\\EditRecord;
use Modules\\{$module}\\Filament\\Resources\\{$plural}\\{$resource};

class Edit{$model} extends EditRecord
{
    protected static string \$resource = {$resource}::class;
}

PHP;
}

function viewPageContent(string $module, string $model): string
{
    $resource = "{$model}Resource";
    $plural = Str::plural($model);

    return <<<PHP
<?php

namespace Modules\\{$module}\\Filament\\Resources\\{$plural}\\Pages;

use Filament\\Resources\\Pages\\ViewRecord;
use Modules\\{$module}\\Filament\\Resources\\{$plural}\\{$resource};

class View{$model} extends ViewRecord
{
    protected static string \$resource = {$resource}::class;
}

PHP;
}

function formSchemaContent(string $module, string $model): string
{
    return <<<PHP
<?php

namespace Modules\\{$module}\\Filament\\Resources\\{$model}s\\Schemas;

use Filament\\Forms\\Components\\Select;
use Filament\\Forms\\Components\\Textarea;
use Filament\\Forms\\Components\\TextInput;
use Filament\\Schemas\\Components\\Section;
use Filament\\Schemas\\Schema;
use Modules\\Core\\Support\\FilamentUi;

class {$model}Form
{
    public static function configure(Schema \$schema): Schema
    {
        return \$schema->components([
            Section::make(FilamentUi::text('General information'))->schema([
                TextInput::make('code')->label(FilamentUi::field('code')),
                TextInput::make('name')->label(FilamentUi::field('name'))->required(),
                Select::make('status')->label(FilamentUi::field('status'))->options([
                    'active' => FilamentUi::text('Active'),
                    'inactive' => FilamentUi::text('Inactive'),
                ])->default('active'),
                Textarea::make('description')->label(FilamentUi::field('description'))->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}

PHP;
}

function tableContent(string $module, string $model): string
{
    return <<<PHP
<?php

namespace Modules\\{$module}\\Filament\\Resources\\{$model}s\\Tables;

use Filament\\Tables\\Columns\\TextColumn;
use Filament\\Tables\\Table;
use Modules\\Core\\Support\\FilamentUi;

class {$model}sTable
{
    public static function configure(Table \$table): Table
    {
        return \$table
            ->columns([
                TextColumn::make('code')->label(FilamentUi::field('code'))->searchable(),
                TextColumn::make('name')->label(FilamentUi::field('name'))->searchable()->sortable(),
                TextColumn::make('status')->label(FilamentUi::field('status'))->badge(),
            ])
            ->defaultSort('name');
    }
}

PHP;
}

function resourceContent(string $module, string $model): string
{
    $plural = "{$model}s";

    return <<<PHP
<?php

namespace Modules\\{$module}\\Filament\\Resources\\{$plural};

use Filament\\Schemas\\Schema;
use Filament\\Tables\\Table;
use Modules\\Core\\Filament\\Support\\ModuleResource;
use Modules\\{$module}\\Filament\\Resources\\{$plural}\\Pages\\Create{$model};
use Modules\\{$module}\\Filament\\Resources\\{$plural}\\Pages\\Edit{$model};
use Modules\\{$module}\\Filament\\Resources\\{$plural}\\Pages\\List{$plural};
use Modules\\{$module}\\Filament\\Resources\\{$plural}\\Pages\\View{$model};
use Modules\\{$module}\\Filament\\Resources\\{$plural}\\Schemas\\{$model}Form;
use Modules\\{$module}\\Filament\\Resources\\{$plural}\\Tables\\{$model}sTable;
use Modules\\{$module}\\Models\\{$model};

class {$model}Resource extends ModuleResource
{
    protected static ?string \$model = {$model}::class;

    protected static ?string \$recordTitleAttribute = 'name';

    public static function form(Schema \$schema): Schema
    {
        return {$model}Form::configure(\$schema);
    }

    public static function table(Table \$table): Table
    {
        return {$model}sTable::configure(\$table);
    }

    public static function getPages(): array
    {
        return [
            'index' => List{$plural}::route('/'),
            'create' => Create{$model}::route('/create'),
            'view' => View{$model}::route('/{record}'),
            'edit' => Edit{$model}::route('/{record}/edit'),
        ];
    }
}

PHP;
}

foreach ($modules as $module => $config) {
    echo "\n=== Scaffolding {$module} ===\n";
    $modulePath = "{$basePath}/Modules/{$module}";
    $primary = $config['primary'];
    $primaryTable = snake($primary).'s';
    if (! str_ends_with($primaryTable, 's')) {
        $primaryTable .= 's';
    }
    // fix irregular plurals
    $primaryTable = match ($primary) {
        'Alumnus' => 'alumni',
        default => Str::plural(Str::snake($primary)),
    };

    writeFile("{$modulePath}/app/Providers/{$module}ServiceProvider.php", serviceProviderContent($module));
    writeFile("{$modulePath}/app/Providers/RouteServiceProvider.php", routeProviderContent($module));
    writeFile("{$modulePath}/app/Providers/EventServiceProvider.php", eventProviderContent($module));

    $moduleJson = json_decode(file_get_contents("{$modulePath}/module.json"), true);
    $moduleJson['providers'] = ["Modules\\{$module}\\Providers\\{$module}ServiceProvider"];
    writeFile("{$modulePath}/module.json", json_encode($moduleJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

    writeFile("{$modulePath}/composer.json", json_encode([
        'name' => 'nwidart/'.strtolower($module),
        'description' => '',
        'authors' => [['name' => 'FoundationOS', 'email' => 'dev@foundationos.test']],
        'extra' => ['laravel' => ['providers' => [], 'aliases' => []]],
        'autoload' => [
            'psr-4' => [
                "Modules\\{$module}\\" => 'app/',
                "Modules\\{$module}\\Database\\Factories\\" => 'database/factories/',
                "Modules\\{$module}\\Database\\Seeders\\" => 'database/seeders/',
            ],
        ],
        'autoload-dev' => [
            'psr-4' => [
                "Modules\\{$module}\\Tests\\" => 'tests/',
            ],
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

    writeFile(
        "{$modulePath}/database/migrations/2026_05_24_000000_create_".strtolower($module).'_tables.php',
        migrationContent($module, $config['tables'])
    );

    foreach ($config['tables'] as $table) {
        $modelName = studly(Str::singular($table));
        if ($modelName === 'Alumnus' && $table === 'alumni') {
            $modelName = 'Alumnus';
        }
        writeFile("{$modulePath}/app/Models/{$modelName}.php", modelContent($module, $modelName, $table));
    }

    $plural = Str::plural($primary);
    writeFile("{$modulePath}/app/Filament/Resources/{$plural}/{$primary}Resource.php", resourceContent($module, $primary));
    writeFile("{$modulePath}/app/Filament/Resources/{$plural}/Schemas/{$primary}Form.php", formSchemaContent($module, $primary));
    writeFile("{$modulePath}/app/Filament/Resources/{$plural}/Tables/{$primary}sTable.php", tableContent($module, $primary));
    writeFile("{$modulePath}/app/Filament/Resources/{$plural}/Pages/List{$plural}.php", listPageContent($module, $primary));
    writeFile("{$modulePath}/app/Filament/Resources/{$plural}/Pages/Create{$primary}.php", createPageContent($module, $primary));
    writeFile("{$modulePath}/app/Filament/Resources/{$plural}/Pages/Edit{$primary}.php", editPageContent($module, $primary));
    writeFile("{$modulePath}/app/Filament/Resources/{$plural}/Pages/View{$primary}.php", viewPageContent($module, $primary));

    writeFile("{$modulePath}/routes/web.php", "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::prefix('".strtolower($module)."')->group(function (): void {\n    //\n});\n");
}

echo "\nDone. Run: composer dump-autoload\n";
