<?php

namespace Modules\Procurement\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Core\Support\FilamentUi;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\Vendor;

class ProcurementStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 40;

    protected function getStats(): array
    {
        $tenantId = Filament::getTenant()?->getKey();

        if (! $tenantId) {
            return [];
        }

        $totalVendors = Vendor::query()
            ->where('tenant_id', $tenantId)
            ->count();

        $activeVendors = Vendor::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('is_blacklisted', false)
            ->count();

        $pendingPurchaseOrders = PurchaseOrder::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['draft', 'pending', 'pending_approval'])
            ->count();

        $poValueThisMonth = (float) PurchaseOrder::query()
            ->where('tenant_id', $tenantId)
            ->whereMonth('po_date', now()->month)
            ->whereYear('po_date', now()->year)
            ->sum('total_amount');

        return [
            Stat::make('Total Vendors', number_format($totalVendors))
                ->description('All registered vendors')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('primary'),
            Stat::make('Active Vendors', number_format($activeVendors))
                ->description('Active and not blacklisted')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Pending Purchase Orders', number_format($pendingPurchaseOrders))
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingPurchaseOrders > 0 ? 'warning' : 'success'),
            Stat::make('Total PO Value This Month', 'Rp '.number_format($poValueThisMonth, 0, ',', '.'))
                ->description('PO total for '.now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Procurement');
    }
}
