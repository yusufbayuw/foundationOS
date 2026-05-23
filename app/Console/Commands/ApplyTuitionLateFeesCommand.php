<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Models\Tenant;
use Modules\School\Services\TuitionLateFeeService;

class ApplyTuitionLateFeesCommand extends Command
{
    protected $signature = 'school:apply-late-fees {--tenant=}';

    protected $description = 'Apply late fees to overdue student invoices';

    public function handle(TuitionLateFeeService $service): int
    {
        $tenantId = $this->option('tenant');
        $tenants = $tenantId
            ? Tenant::query()->whereKey($tenantId)->get()
            : Tenant::query()->where('status', 'active')->get();

        $total = 0;
        foreach ($tenants as $tenant) {
            $count = $service->applyForTenant((int) $tenant->id);
            $total += $count;
            $this->info("Tenant {$tenant->id}: applied late fees to {$count} invoice(s).");
        }

        $this->info("Done. Total updated: {$total}.");

        return self::SUCCESS;
    }
}
