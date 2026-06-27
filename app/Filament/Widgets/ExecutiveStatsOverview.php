<?php

namespace App\Filament\Widgets;

use App\Services\ExecutiveMetricsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Core\Support\FilamentUi;

class ExecutiveStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenant = current_tenant_model();
        if (! $tenant) {
            return [];
        }

        $metrics = app(ExecutiveMetricsService::class)->forTenant((int) $tenant->getKey());
        $activeTenants = auth()->user()?->isGlobalSuperAdmin()
            ? app(ExecutiveMetricsService::class)->activeTenantsCount()
            : 1;

        $formatMoney = fn (float $amount): string => 'Rp '.number_format($amount, 0, ',', '.');

        return [
            Stat::make(FilamentUi::text('Revenue MTD'), $formatMoney($metrics['revenue_mtd']))
                ->description(FilamentUi::text('Verified payments this month'))
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make(FilamentUi::text('Payroll MTD'), $formatMoney($metrics['payroll_mtd']))
                ->description(FilamentUi::text('Approved and paid salary slips'))
                ->icon('heroicon-o-identification')
                ->color('warning'),
            Stat::make(FilamentUi::text('Outstanding AP'), $formatMoney($metrics['outstanding_ap']))
                ->description(FilamentUi::text('Vendor bills not fully paid'))
                ->icon('heroicon-o-truck')
                ->color('danger'),
            Stat::make(FilamentUi::text('Outstanding AR'), $formatMoney($metrics['outstanding_ar']))
                ->description(FilamentUi::text('Student invoices outstanding'))
                ->icon('heroicon-o-document-text')
                ->color('danger'),
            Stat::make(FilamentUi::text('Active Tenants'), (string) $activeTenants)
                ->description(FilamentUi::text('Active SaaS accounts'))
                ->icon('heroicon-o-building-office')
                ->color('info'),
            Stat::make(FilamentUi::text('Pending Approvals'), (string) $metrics['pending_approvals'])
                ->description(FilamentUi::text('Workflow instances in progress'))
                ->icon('heroicon-o-clock')
                ->color($metrics['pending_approvals'] > 0 ? 'warning' : 'success'),
        ];
    }
}
