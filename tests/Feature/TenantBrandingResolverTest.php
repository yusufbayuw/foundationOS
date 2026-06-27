<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Support\Filament\EnabledModuleRegistry;
use Modules\Core\Support\Filament\TenantBrandingResolver;
use Tests\TestCase;

class TenantBrandingResolverTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_resolves_branding_settings_in_a_single_query_per_request(): void
    {
        $tenant = $this->makeTenant();

        TenantSetting::create([
            'tenant_id' => $tenant->id,
            'group' => 'branding',
            'key' => 'primary_color',
            'value' => '#ff5500',
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

        DB::enableQueryLog();

        $branding = $resolver->forTenant($tenant);
        $colors = $branding->filamentColors();
        $logo = $resolver->forTenant($tenant)->filamentLogoUrl();

        $settingQueries = collect(DB::getQueryLog())
            ->filter(fn (array $query): bool => str_contains($query['query'], 'tenant_settings'));

        $this->assertCount(1, $settingQueries);
        $this->assertSame('#ff5500', $branding->primaryColor);
        $this->assertStringContainsString('tenant-logos/logo.png', (string) $logo);
    }

    public function test_forget_invalidates_cached_branding(): void
    {
        $tenant = $this->makeTenant();

        TenantSetting::create([
            'tenant_id' => $tenant->id,
            'group' => 'branding',
            'key' => 'primary_color',
            'value' => '#111111',
            'type' => 'string',
        ]);

        Cache::flush();

        $resolver = app(TenantBrandingResolver::class);
        $resolver->forTenant($tenant);

        TenantSetting::query()
            ->where('tenant_id', $tenant->id)
            ->where('key', 'primary_color')
            ->update(['value' => '#222222']);

        $resolver->forget($tenant->id);

        $updated = $resolver->forTenant($tenant);

        $this->assertSame('#222222', $updated->primaryColor);
    }

    public function test_enabled_module_registry_memoizes_module_list(): void
    {
        $registry = app(EnabledModuleRegistry::class);

        $first = $registry->all();
        $second = $registry->all();

        $this->assertSame($first, $second);
        $this->assertNotEmpty($registry->navigationGroups());
    }

    protected function makeTenant(): Tenant
    {
        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'brand-'.Str::random(4),
            'name' => 'Branding Tenant',
            'status' => 'active',
        ]);
    }
}
