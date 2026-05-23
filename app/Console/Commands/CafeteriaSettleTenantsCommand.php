<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Cafeteria\Models\CafeteriaTenant;
use Modules\Cafeteria\Models\CafeteriaTransaction;

class CafeteriaSettleTenantsCommand extends Command
{
    protected $signature = 'cafeteria:settle-tenants {--period=weekly}';

    protected $description = 'Settle cafeteria tenant revenue share for the current period';

    public function handle(): int
    {
        $since = $this->option('period') === 'monthly'
            ? now()->startOfMonth()
            : now()->startOfWeek();

        $tenants = CafeteriaTenant::query()->where('status', 'active')->get();

        foreach ($tenants as $tenant) {
            $total = CafeteriaTransaction::query()
                ->where('tenant_id', $tenant->tenant_id)
                ->where('meta->cafeteria_tenant_id', $tenant->getKey())
                ->where('created_at', '>=', $since)
                ->sum('meta->amount');

            $tenant->update([
                'meta' => array_merge($tenant->meta ?? [], [
                    'last_settlement_at' => now()->toIso8601String(),
                    'last_settlement_amount' => $total,
                ]),
            ]);
        }

        $this->info('Cafeteria tenant settlement completed for '.$tenants->count().' tenant(s).');

        return self::SUCCESS;
    }
}
