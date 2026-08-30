<?php

namespace Modules\Finance\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Models\Budget;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;

class FinanceStatsWidget extends StatsOverviewWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 20;

    protected function getStats(): array
    {
        $tenantId = Filament::getTenant()?->getKey();

        if (! $tenantId) {
            return [];
        }

        $outstandingAmount = (float) StudentInvoice::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['issued', 'partial'])
            ->sum('remaining_amount');

        $pendingPayments = Payment::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->count();

        $verifiedPaymentsAmount = (float) Payment::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'verified')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $postedJournals = JournalEntry::query()
            ->where('tenant_id', $tenantId)
            ->where('is_posted', true)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        $budgetAllocated = (float) Budget::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->sum('allocated_amount');

        $budgetUsed = (float) Budget::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->sum('used_amount');

        $budgetUsagePercent = $budgetAllocated > 0
            ? round(($budgetUsed / $budgetAllocated) * 100, 1).'%'
            : '-';

        return [
            Stat::make('Tagihan Outstanding', 'Rp '.number_format($outstandingAmount, 0, ',', '.'))
                ->description('Total sisa tagihan belum lunas')
                ->icon('heroicon-o-document-text')
                ->color($outstandingAmount > 0 ? 'danger' : 'success'),
            Stat::make('Pembayaran Pending', number_format($pendingPayments))
                ->description('Menunggu verifikasi')
                ->icon('heroicon-o-clock')
                ->color($pendingPayments > 0 ? 'warning' : 'success'),
            Stat::make('Pembayaran Bulan Ini', 'Rp '.number_format($verifiedPaymentsAmount, 0, ',', '.'))
                ->description('Total verified '.now()->translatedFormat('F Y'))
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make('Realisasi Anggaran', $budgetUsagePercent)
                ->description('Rp '.number_format($budgetUsed, 0, ',', '.').' / Rp '.number_format($budgetAllocated, 0, ',', '.'))
                ->icon('heroicon-o-chart-pie')
                ->color('info'),
            Stat::make('Jurnal Posted', number_format($postedJournals))
                ->description('Bulan '.now()->translatedFormat('F Y'))
                ->icon('heroicon-o-document-check')
                ->color('info'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Finance');
    }
}
