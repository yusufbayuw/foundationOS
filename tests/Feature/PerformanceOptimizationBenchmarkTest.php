<?php

namespace Tests\Feature;

use App\Filament\Widgets\Charts\Concerns\CachesChartData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Filament\Support\Navigation\ModuleVisibility;
use Modules\Core\Models\Module;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Support\Filament\TenantBrandingResolver;
use Modules\Core\Support\TenantSettingsResolver;
use Modules\Finance\Models\Payment;
use Tests\TestCase;

class PerformanceOptimizationBenchmarkTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** Baseline: two separate TenantSetting lookups per panel branding render. */
    private const BASELINE_BRANDING_SETTING_QUERIES = 2;

    /** After: one grouped settings query serves colors + logo. */
    private const OPTIMIZED_BRANDING_SETTING_QUERIES = 1;

    /** Baseline: one exists() query per module visibility check (cold cache). */
    private const BASELINE_MODULE_VISIBILITY_QUERIES_PER_CHECK = 1;

    /** After: one batch query loads all enabled module codes (cold cache). */
    private const OPTIMIZED_MODULE_VISIBILITY_QUERIES_FOR_FIVE_CHECKS = 1;

    /** Baseline: one sum() query per chart day (30-day window). */
    private const BASELINE_CHART_DAILY_QUERIES = 30;

    /** After: one grouped SQL query for the full chart window. */
    private const OPTIMIZED_CHART_DAILY_QUERIES = 1;

    public function test_tenant_branding_resolver_reduces_repeated_setting_queries(): void
    {
        $tenant = $this->makeTenant();

        TenantSetting::create([
            'tenant_id' => $tenant->id,
            'group' => 'branding',
            'key' => 'primary_color',
            'value' => '#336699',
            'type' => 'string',
        ]);

        TenantSetting::create([
            'tenant_id' => $tenant->id,
            'group' => 'branding',
            'key' => 'brand_logo',
            'value' => 'tenant-logos/logo.png',
            'type' => 'string',
        ]);

        Cache::flush();

        $resolver = app(TenantBrandingResolver::class);

        $queries = $this->countSettingQueries(function () use ($resolver, $tenant): void {
            $branding = $resolver->forTenant($tenant);
            $branding->filamentColors();
            $resolver->forTenant($tenant)->filamentLogoUrl();
        });

        $this->assertSame(self::BASELINE_BRANDING_SETTING_QUERIES - 1, self::OPTIMIZED_BRANDING_SETTING_QUERIES);
        $this->assertSame(self::OPTIMIZED_BRANDING_SETTING_QUERIES, $queries);
        $this->assertLessThan(self::BASELINE_BRANDING_SETTING_QUERIES, $queries);
    }

    public function test_module_visibility_batch_loads_enabled_modules_in_one_query(): void
    {
        $tenant = $this->makeTenant();

        foreach (['school', 'finance', 'library'] as $code) {
            $module = Module::create([
                'code' => $code,
                'slug' => $code,
                'name' => str($code)->headline()->toString(),
                'is_core' => false,
                'is_active' => true,
            ]);

            TenantModule::create([
                'tenant_id' => $tenant->id,
                'module_id' => $module->id,
                'is_enabled' => true,
            ]);
        }

        Cache::flush();

        $queries = $this->countTenantModuleQueries(function () use ($tenant): void {
            for ($i = 0; $i < 8; $i++) {
                ModuleVisibility::enabledModuleCodes($tenant->id);
            }
        });

        $baseline = self::BASELINE_MODULE_VISIBILITY_QUERIES_PER_CHECK * 8;

        $this->assertSame(self::OPTIMIZED_MODULE_VISIBILITY_QUERIES_FOR_FIVE_CHECKS, $queries);
        $this->assertLessThan($baseline, $queries);
    }

    public function test_chart_daily_sum_series_uses_single_grouped_query(): void
    {
        $tenant = $this->makeTenant();

        $helper = new class
        {
            use CachesChartData;

            /** @param  \Closure(Builder): void  $scope */
            public function build(string $modelClass, int $tenantId, int $days, string $dateColumn, string $sumColumn, \Closure $scope): array
            {
                return $this->dailySumSeries($modelClass, $tenantId, $days, $dateColumn, $sumColumn, $scope);
            }
        };

        $queries = $this->countPaymentQueries(fn () => $helper->build(
            Payment::class,
            $tenant->id,
            self::BASELINE_CHART_DAILY_QUERIES,
            'payment_date',
            'amount',
            fn ($query) => $query->where('status', 'verified'),
        ));

        $this->assertSame(self::OPTIMIZED_CHART_DAILY_QUERIES, $queries);
        $this->assertLessThan(self::BASELINE_CHART_DAILY_QUERIES, $queries);
    }

    public function test_tenant_settings_resolver_caches_finance_group_for_repeated_reads(): void
    {
        $tenant = $this->makeTenant();

        TenantSetting::create([
            'tenant_id' => $tenant->id,
            'group' => 'finance',
            'key' => 'default_receivable_account_id',
            'value' => '42',
            'type' => 'integer',
        ]);

        Cache::flush();

        $resolver = app(TenantSettingsResolver::class);

        $queries = $this->countSettingQueries(function () use ($resolver, $tenant): void {
            $resolver->value($tenant->id, 'finance', 'default_receivable_account_id');
            $resolver->value($tenant->id, 'finance', 'default_receivable_account_id');
            $resolver->group($tenant->id, 'finance');
        });

        $this->assertSame(1, $queries);
    }

    protected function makeTenant(): Tenant
    {
        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'perf-'.Str::random(4),
            'name' => 'Performance Tenant',
            'status' => 'active',
        ]);
    }

    protected function countSettingQueries(callable $callback): int
    {
        return $this->countQueriesMatching($callback, 'tenant_settings');
    }

    protected function countTenantModuleQueries(callable $callback): int
    {
        return $this->countQueriesMatching($callback, 'tenant_modules');
    }

    protected function countPaymentQueries(callable $callback): int
    {
        return $this->countQueriesMatching($callback, 'payments');
    }

    protected function countQueriesMatching(callable $callback, string $tableFragment): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $callback();

        return collect(DB::getQueryLog())
            ->filter(fn (array $query): bool => str_contains($query['query'], $tableFragment))
            ->count();
    }
}
