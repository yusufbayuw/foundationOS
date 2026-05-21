<?php

namespace Modules\Enrollment\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Core\Support\FilamentUi;
use Modules\Enrollment\Models\AdmissionPeriod;
use Modules\Enrollment\Models\Applicant;

class EnrollmentStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 11;

    protected function getStats(): array
    {
        if (! Filament::getTenant()) {
            return [];
        }

        return [
            Stat::make('Total Applicants', Applicant::count())
                ->description('All registered applicants')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary'),
            Stat::make('Pending Applications', Applicant::whereIn('status', ['registered', 'screening'])->count())
                ->description('Awaiting decision')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Accepted', Applicant::where('status', 'accepted')->count())
                ->description('Accepted applicants')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Active Admission Period', AdmissionPeriod::where('is_active', true)->count())
                ->description('Open admission periods')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Enrollment');
    }
}
