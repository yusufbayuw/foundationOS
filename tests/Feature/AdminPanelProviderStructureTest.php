<?php

namespace Tests\Feature;

use App\Providers\Filament\Admin\AdminPanelAuthConfigurator;
use App\Providers\Filament\Admin\AdminPanelBrandingConfigurator;
use App\Providers\Filament\Admin\AdminPanelDiscoveryConfigurator;
use App\Providers\Filament\Admin\AdminPanelNavigationConfigurator;
use App\Providers\Filament\Admin\AdminPanelPluginConfigurator;
use App\Providers\Filament\Admin\AdminPanelUserMenuConfigurator;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminPanelProviderStructureTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_panel_provider_is_a_thin_facade_over_configurators(): void
    {
        $source = file_get_contents(base_path('app/Providers/Filament/AdminPanelProvider.php'));

        $this->assertLessThan(45, substr_count($source, "\n"));
        $this->assertStringContainsString('AdminPanelAuthConfigurator', $source);
        $this->assertStringContainsString('AdminPanelBrandingConfigurator', $source);
        $this->assertStringContainsString('AdminPanelDiscoveryConfigurator', $source);
        $this->assertStringNotContainsString('TenantSetting::query', $source);
        $this->assertStringNotContainsString('Module::allEnabled', $source);
    }

    public function test_admin_panel_configurators_are_container_resolvable(): void
    {
        $this->assertInstanceOf(AdminPanelAuthConfigurator::class, app(AdminPanelAuthConfigurator::class));
        $this->assertInstanceOf(AdminPanelBrandingConfigurator::class, app(AdminPanelBrandingConfigurator::class));
        $this->assertInstanceOf(AdminPanelNavigationConfigurator::class, app(AdminPanelNavigationConfigurator::class));
        $this->assertInstanceOf(AdminPanelDiscoveryConfigurator::class, app(AdminPanelDiscoveryConfigurator::class));
        $this->assertInstanceOf(AdminPanelPluginConfigurator::class, app(AdminPanelPluginConfigurator::class));
        $this->assertInstanceOf(AdminPanelUserMenuConfigurator::class, app(AdminPanelUserMenuConfigurator::class));
    }
}
