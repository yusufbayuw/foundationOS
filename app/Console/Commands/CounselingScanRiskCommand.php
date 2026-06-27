<?php

namespace App\Console\Commands;

use App\Support\TypedValue;
use Illuminate\Console\Command;
use Modules\Core\Models\Tenant;
use Modules\Counseling\Services\CounselingRiskScanner;

class CounselingScanRiskCommand extends Command
{
    protected $signature = 'counseling:scan-risk {--tenant=}';

    protected $description = 'Scan students for counseling risk flags and auto-create cases';

    public function handle(CounselingRiskScanner $scanner): int
    {
        $tenantOption = $this->option('tenant');
        $tenants = Tenant::query()
            ->when($tenantOption, fn ($q) => $q->whereKey($tenantOption))
            ->pluck('id');

        $total = 0;
        foreach ($tenants as $tenantId) {
            $created = $scanner->scanTenant(TypedValue::int($tenantId));
            $total += $created;
            $this->line('Tenant '.TypedValue::string($tenantId).": {$created} case(s) created.");
        }

        $this->info("Total new counseling cases: {$total}");

        return self::SUCCESS;
    }
}
