<?php

namespace App\Providers\Filament\Admin;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Coolsam\Modules\ModulesPlugin;
use Filament\Panel;

class AdminPanelPluginConfigurator
{
    public function configure(Panel $panel): Panel
    {
        return $panel->plugins([
            FilamentShieldPlugin::make(),
            ModulesPlugin::make(),
        ]);
    }
}
