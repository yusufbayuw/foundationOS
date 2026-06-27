<?php

namespace App\Providers\Filament\Admin;

use Filament\Panel;
use Modules\Core\Support\Filament\EnabledModuleRegistry;

class AdminPanelDiscoveryConfigurator
{
    public function __construct(
        private readonly EnabledModuleRegistry $modules,
    ) {}

    public function configure(Panel $panel): Panel
    {
        $panel = $panel
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets');

        foreach ($this->modules->all() as $module) {
            $panel = $panel
                ->discoverResources(
                    in: $module->appPath('Filament/Resources'),
                    for: $module->appNamespace('Filament\\Resources'),
                )
                ->discoverPages(
                    in: $module->appPath('Filament/Pages'),
                    for: $module->appNamespace('Filament\\Pages'),
                )
                ->discoverWidgets(
                    in: $module->appPath('Filament/Widgets'),
                    for: $module->appNamespace('Filament\\Widgets'),
                );
        }

        return $panel;
    }
}
