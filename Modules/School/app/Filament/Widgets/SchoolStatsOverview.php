<?php

namespace Modules\School\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Core\Support\FilamentUi;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Models\Teacher;

class SchoolStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 11;

    protected function getStats(): array
    {
        if (! Filament::getTenant()) {
            return [];
        }

        return [
            Stat::make('Total Students', Student::count())
                ->description('All registered students')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),
            Stat::make('Active Students', Student::where('status', 'active')->count())
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Total Teachers', Teacher::count())
                ->description('All teachers')
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),
            Stat::make('Total Classes', SchoolClass::count())
                ->description('All classes')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('warning'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('School');
    }
}
