<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(meta, '$.cafeteria_tenant_id')) = ?", [(string) $tenant->getKey()])
                ->where('created_at', '>=', $since)
                ->sum(DB::raw("CAST(JSON_UNQUOTE(JSON_EXTRACT(meta, '$.amount')) AS DECIMAL(15,2))"));

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
