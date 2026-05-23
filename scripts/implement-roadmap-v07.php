<?php

/**
 * Implement ROADMAP v07 — Revenue Engine (migrations, models, services, commands, new modules).
 *
 * Usage: php scripts/implement-roadmap-v07.php
 */

declare(strict_types=1);

require dirname(__DIR__).'/vendor/autoload.php';

use Illuminate\Support\Str;

$base = dirname(__DIR__);

function write(string $path, string $content): void
{
    $dir = dirname($path);
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($path, $content);
    echo "  wrote {$path}\n";
}

function moduleProvider(string $module, string $lower): string
{
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

function routeProvider(string $module): string
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

function eventProvider(string $module): string
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

function modelStub(string $module, string $model, string $table, string $extra = ''): string
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
{$extra}
}

PHP;
}

function scaffoldModule(string $base, string $module, string $lower, array $tables, string $primaryModel, string $primaryTable): void
{
    $root = "{$base}/Modules/{$module}";
    write("{$root}/module.json", json_encode([
        'name' => $module,
        'alias' => $lower,
        'description' => '',
        'keywords' => [],
        'priority' => 0,
        'providers' => ["Modules\\{$module}\\Providers\\{$module}ServiceProvider"],
        'files' => [],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

    write("{$root}/composer.json", json_encode([
        'name' => "nwidart/{$lower}",
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
            'psr-4' => ["Modules\\{$module}\\Tests\\" => 'tests/'],
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

    write("{$root}/app/Providers/{$module}ServiceProvider.php", moduleProvider($module, $lower));
    write("{$root}/app/Providers/RouteServiceProvider.php", routeProvider($module));
    write("{$root}/app/Providers/EventServiceProvider.php", eventProvider($module));
    write("{$root}/routes/web.php", "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n");

    $creates = '';
    foreach ($tables as $table) {
        $creates .= "\n        Schema::create('{$table}', function (Blueprint \$table) {\n";
        $creates .= "            \$table->id();\n";
        $creates .= "            \$table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();\n";
        $creates .= "            \$table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();\n";
        $creates .= "            \$table->string('code')->nullable();\n";
        $creates .= "            \$table->string('name')->nullable();\n";
        $creates .= "            \$table->string('status')->default('active');\n";
        $creates .= "            \$table->text('description')->nullable();\n";
        $creates .= "            \$table->json('meta')->nullable();\n";
        $creates .= "            \$table->timestamps();\n";
        $creates .= "            \$table->softDeletes();\n";
        $creates .= "        });\n";
    }
    $drops = implode("\n        ", array_map(fn ($t) => "Schema::dropIfExists('{$t}');", array_reverse($tables)));

    write("{$root}/database/migrations/2026_05_24_000000_create_{$lower}_tables.php", <<<PHP
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

PHP);

    foreach ($tables as $table) {
        $model = Str::studly(Str::singular($table));
        if ($table === 'alumni') {
            $model = 'Alumnus';
        }
        write("{$root}/app/Models/{$model}.php", modelStub($module, $model, $table));
    }
}

echo "ROADMAP v07 implementation\n";

// --- Enrollment CRM migration ---
write("{$base}/Modules/Enrollment/database/migrations/2026_05_24_100000_create_enrollment_crm_tables.php", file_get_contents("{$base}/scripts/templates/v07-enrollment-crm-migration.php"));

// --- Cooperative sales ---
write("{$base}/Modules/Sales/database/migrations/2026_05_24_100000_create_cooperative_tables.php", file_get_contents("{$base}/scripts/templates/v07-cooperative-migration.php"));

// --- Facility rental ---
write("{$base}/Modules/Facility/database/migrations/2026_05_24_100000_add_facility_rental_tables.php", file_get_contents("{$base}/scripts/templates/v07-facility-rental-migration.php"));

// Replace stub migrations with proper schemas
foreach (['cms' => 'Cms', 'donation' => 'Donation', 'training' => 'Training'] as $file => $mod) {
    $tpl = "{$base}/scripts/templates/v07-{$file}-migration.php";
    if (is_file($tpl)) {
        write("{$base}/Modules/{$mod}/database/migrations/2026_05_24_000000_create_{$file}_tables.php", file_get_contents($tpl));
    }
}

// New modules
$newModules = [
    'Printing' => ['print_orders', 'print_order_items', 'print_templates', 'print_productions', 'print_materials', 'publications', 'publication_authors', 'royalties'],
    'Consulting' => ['consulting_clients', 'consulting_engagements', 'engagement_proposals', 'engagement_deliverables', 'consultant_timesheets', 'engagement_invoices'],
    'Property' => ['properties', 'commercial_tenants', 'lease_agreements', 'lease_invoices', 'property_maintenances', 'lease_deposits'],
    'Marketplace' => ['sellers', 'marketplace_products', 'marketplace_product_categories', 'marketplace_product_variants', 'marketplace_orders', 'marketplace_order_items', 'marketplace_order_shipments', 'marketplace_product_reviews'],
];

foreach ($newModules as $module => $tables) {
    scaffoldModule($base, $module, strtolower($module), $tables, Str::studly(Str::singular($tables[0])), $tables[0]);
}

echo "Done. Run: composer dump-autoload && php artisan migrate\n";
