<?php

namespace Modules\Campus\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Lecturer;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Support\FilamentUi;

class CampusStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 11;

    protected function getStats(): array
    {
        if (! Filament::getTenant()) {
            return [];
        }

        return [
            Stat::make('Total College Students', CollageStudent::count())
                ->description('All registered college students')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),
            Stat::make('Total Lecturers', Lecturer::count())
                ->description('All lecturers')
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),
            Stat::make('Active Course Offerings', CourseOffering::where('status', 'active')->count())
                ->description('Currently running courses')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),
            Stat::make('Total Study Programs', StudyProgram::count())
                ->description('All study programs')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('warning'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Campus');
    }
}
