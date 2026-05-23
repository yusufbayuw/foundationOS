<?php

namespace App\Filament\Platform\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Core\Models\SubscriptionLog;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\User;
use Modules\Core\Support\CurrencyFormatter;

class PlatformStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $trialTenants = Tenant::where('status', 'trial')->count();
        $newThisMonth = Tenant::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $totalUsers = User::count();
        $activeModules = TenantModule::where('is_enabled', true)->count();

        $mrr = $this->calculateMrr();
        $paidThisMonth = SubscriptionLog::where('payment_status', 'paid')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('amount');

        return [
            Stat::make('Total Tenants', $totalTenants)
                ->description("{$activeTenants} active · {$trialTenants} trial · {$newThisMonth} new this month")
                ->color('primary'),

            Stat::make('MRR (Estimated)', CurrencyFormatter::format($mrr, 'IDR'))
                ->description('Monthly Recurring Revenue from active plans')
                ->color('success'),

            Stat::make('Collected This Month', CurrencyFormatter::format((float) $paidThisMonth, 'IDR'))
                ->description('Paid invoices in '.now()->format('M Y'))
                ->color('info'),

            Stat::make('Total Users', $totalUsers)
                ->description('Across all tenants')
                ->color('gray'),

            Stat::make('Active Module Slots', $activeModules)
                ->description('Enabled module×tenant combinations')
                ->color('warning'),
        ];
    }

    private function calculateMrr(): float
    {
        return Tenant::query()
            ->where('status', 'active')
            ->whereNotNull('subscription_plan_id')
            ->with(['subscriptionPlan', 'users'])
            ->get()
            ->sum(function (Tenant $tenant): float {
                $plan = $tenant->subscriptionPlan;
                if (! $plan) {
                    return 0.0;
                }

                $activeSeats = $tenant->users()->count();
                $activeModules = TenantModule::where('tenant_id', $tenant->getKey())
                    ->where('is_enabled', true)
                    ->count();

                return $plan->calculateMonthlyAmount($activeSeats, $activeModules);
            });
    }
}
