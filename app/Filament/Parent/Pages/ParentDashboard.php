<?php

namespace App\Filament\Parent\Pages;

use Filament\Pages\Dashboard;
use Modules\Core\Support\FilamentUi;

class ParentDashboard extends Dashboard
{
    protected static ?string $navigationLabel = null;

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Dashboard');
    }
}
