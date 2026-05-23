<?php

namespace App\Filament\Platform\Pages;

use App\Filament\Platform\Widgets\PlatformStatsOverview;
use App\Filament\Platform\Widgets\RecentTenantsWidget;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;

class PlatformDashboard extends Dashboard
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::RectangleGroup;

    protected static string $routePath = '/';

    public function getTitle(): string
    {
        return 'Platform Overview';
    }

    public function getWidgets(): array
    {
        return [
            PlatformStatsOverview::class,
            RecentTenantsWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 3;
    }
}
