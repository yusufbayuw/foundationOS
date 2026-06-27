<?php

namespace App\Providers\Filament;

use App\Providers\Filament\Admin\AdminPanelAuthConfigurator;
use App\Providers\Filament\Admin\AdminPanelBrandingConfigurator;
use App\Providers\Filament\Admin\AdminPanelDiscoveryConfigurator;
use App\Providers\Filament\Admin\AdminPanelNavigationConfigurator;
use App\Providers\Filament\Admin\AdminPanelPluginConfigurator;
use App\Providers\Filament\Admin\AdminPanelUserMenuConfigurator;
use Filament\Panel;
use Filament\PanelProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css');

        $panel = app(AdminPanelAuthConfigurator::class)->configure($panel);
        $panel = app(AdminPanelBrandingConfigurator::class)->configure($panel);
        $panel = app(AdminPanelNavigationConfigurator::class)->configure($panel);
        $panel = app(AdminPanelDiscoveryConfigurator::class)->configure($panel);
        $panel = app(AdminPanelPluginConfigurator::class)->configure($panel);
        $panel = app(AdminPanelUserMenuConfigurator::class)->configure($panel);

        return $panel;
    }
}
