<?php

namespace Modules\Employee\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Core\Support\FilamentUi;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmploymentContract;
use Modules\Employee\Models\LeaveRequest;

class EmployeeStatsOverview extends StatsOverviewWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 30;

    protected function getStats(): array
    {
        $tenantId = Filament::getTenant()?->getKey();

        if (! $tenantId) {
            return [];
        }

        $totalEmployees = Employee::query()
            ->where('tenant_id', $tenantId)
            ->count();

        $activeContracts = EmploymentContract::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->count();

        $pendingLeaveRequests = LeaveRequest::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->count();

        return [
            Stat::make('Total Employees', number_format($totalEmployees))
                ->description('All employees')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Active Contracts', number_format($activeContracts))
                ->description('Currently active contracts')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('success'),
            Stat::make('Pending Leave Requests', number_format($pendingLeaveRequests))
                ->description('Awaiting approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingLeaveRequests > 0 ? 'warning' : 'success'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Employee');
    }
}
