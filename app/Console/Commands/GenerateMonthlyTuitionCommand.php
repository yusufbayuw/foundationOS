<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Models\Tenant;
use Modules\School\Services\MonthlyTuitionInvoiceService;

class GenerateMonthlyTuitionCommand extends Command
{
    protected $signature = 'school:generate-monthly-tuition {--tenant=} {--month=}';

    protected $description = 'Generate monthly student invoices from active tuition types';

    public function handle(MonthlyTuitionInvoiceService $service): int
    {
        $month = $this->option('month') ?? now()->format('Y-m');
        $tenantId = $this->option('tenant');

        $tenants = $tenantId
            ? Tenant::query()->whereKey($tenantId)->get()
            : Tenant::query()->where('status', 'active')->get();

        $total = 0;
        foreach ($tenants as $tenant) {
            $created = $service->generateForTenant((int) $tenant->id, $month);
            $total += $created;
            $this->info("Tenant {$tenant->id}: created {$created} invoice(s) for {$month}.");
        }

        $this->info("Done. Total invoices created: {$total}.");

        return self::SUCCESS;
    }
}
