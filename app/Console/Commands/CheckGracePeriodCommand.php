<?php

namespace App\Console\Commands;

use App\Support\TypedValue;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\Tenant;

#[Signature('fos:billing:check-grace-period')]
#[Description('Check expired subscriptions, set past_due + grace period, suspend locked tenants')]
class CheckGracePeriodCommand extends Command
{
    public function handle(): int
    {
        $now = now();
        $locked = 0;
        $pastDue = 0;
        $cleared = 0;

        // Active tenants with expired subscription → move to past_due + start grace period
        Tenant::query()
            ->where('status', 'active')
            ->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<', $now)
            ->whereNotNull('subscription_plan_id')
            ->with('subscriptionPlan')
            ->each(function (Tenant $tenant) use (&$pastDue): void {
                $graceDays = $tenant->subscriptionPlan->grace_period_days ?? 7;
                $tenant->update([
                    'status' => 'past_due',
                    'grace_period_ends_at' => now()->addDays($graceDays),
                ]);
                $this->clearTenantCache($tenant);
                $this->line("  [past_due] [{$tenant->code}] — grace period ends in {$graceDays} days.");
                $pastDue++;
            });

        // Past-due tenants with expired grace period → suspend
        Tenant::query()
            ->where('status', 'past_due')
            ->whereNotNull('grace_period_ends_at')
            ->where('grace_period_ends_at', '<', $now)
            ->each(function (Tenant $tenant) use (&$locked): void {
                $tenant->update(['status' => 'suspended']);
                $this->clearTenantCache($tenant);
                $this->warn("  [suspended] [{$tenant->code}] — grace period expired.");
                $locked++;
            });

        // Trial tenants with expired trial → set past_due
        Tenant::query()
            ->where('status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', $now)
            ->each(function (Tenant $tenant) use (&$pastDue): void {
                $graceDays = $tenant->subscriptionPlan->grace_period_days ?? 3;
                $tenant->update([
                    'status' => 'past_due',
                    'grace_period_ends_at' => now()->addDays($graceDays),
                ]);
                $this->clearTenantCache($tenant);
                $this->line("  [trial_expired → past_due] [{$tenant->code}]");
                $pastDue++;
            });

        $this->info("Grace period check complete. past_due: {$pastDue}, suspended: {$locked}");

        return self::SUCCESS;
    }

    private function clearTenantCache(Tenant $tenant): void
    {
        Cache::forget('tenant_module_active:'.TypedValue::string($tenant->getKey()).':*');
    }
}
