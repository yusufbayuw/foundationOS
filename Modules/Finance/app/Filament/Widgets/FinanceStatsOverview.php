<?php

namespace Modules\Finance\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;

class FinanceStatsOverview extends StatsOverviewWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 21;

    protected function getStats(): array
    {
        $tenantId = Filament::getTenant()?->getKey();

        if (! $tenantId) {
            return [];
        }

        $outstandingInvoices = (float) StudentInvoice::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['issued', 'partially_paid', 'partial'])
            ->sum('remaining_amount');

        $paidThisMonth = (float) Payment::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'verified')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $pendingPayments = Payment::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->count();

        $revenueYtd = (float) Payment::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'verified')
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        return [
            Stat::make('Total Outstanding Invoices', 'Rp '.number_format($outstandingInvoices, 0, ',', '.'))
                ->description('Unpaid invoice balance')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($outstandingInvoices > 0 ? 'danger' : 'success'),
            Stat::make('Total Paid This Month', 'Rp '.number_format($paidThisMonth, 0, ',', '.'))
                ->description('Verified payments '.now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Pending Payments', number_format($pendingPayments))
                ->description('Awaiting verification')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingPayments > 0 ? 'warning' : 'success'),
            Stat::make('Total Revenue YTD', 'Rp '.number_format($revenueYtd, 0, ',', '.'))
                ->description('Year '.now()->year)
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Finance');
    }
}
