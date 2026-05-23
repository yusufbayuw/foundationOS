<?php

namespace Modules\Employee\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Models\Tenant;
use Modules\Employee\Models\Employee;
use Modules\Employee\Services\PayrollCalculationService;

class GeneratePayrollCommand extends Command
{
    protected $signature = 'employee:generate-payroll
                            {--tenant= : Tenant ID}
                            {--month= : Month (1–12), defaults to current month}
                            {--year= : Year, defaults to current year}
                            {--dry-run : Calculate without saving}';

    protected $description = 'Generate payroll (salary slips) for all active employees of a tenant';

    public function handle(PayrollCalculationService $service): int
    {
        $tenantId = $this->option('tenant');
        $month = (int) ($this->option('month') ?: now()->month);
        $year = (int) ($this->option('year') ?: now()->year);
        $dryRun = (bool) $this->option('dry-run');

        if (! $tenantId) {
            $this->error('--tenant is required.');

            return self::FAILURE;
        }

        $tenant = Tenant::find($tenantId);
        if (! $tenant) {
            $this->error("Tenant #{$tenantId} not found.");

            return self::FAILURE;
        }

        $this->info(sprintf(
            '%sGenerating payroll for tenant "%s" — %s/%s',
            $dryRun ? '[DRY RUN] ' : '',
            $tenant->name,
            str_pad((string) $month, 2, '0', STR_PAD_LEFT),
            $year,
        ));

        $employees = Employee::query()
            ->where('tenant_id', $tenantId)
            ->where('employment_status', 'active')
            ->get();

        if ($employees->isEmpty()) {
            $this->warn('No active employees found.');

            return self::SUCCESS;
        }

        $success = 0;
        $failed = 0;

        foreach ($employees as $employee) {
            try {
                $slip = $service->calculate($employee, $month, $year, $dryRun);
                $this->line(sprintf(
                    '  ✓ %-30s  net = %s',
                    $employee->full_name,
                    number_format((float) $slip->net_salary, 0, ',', '.'),
                ));
                $success++;
            } catch (\Throwable $e) {
                $this->error(sprintf('  ✗ %s — %s', $employee->full_name, $e->getMessage()));
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Done: {$success} success, {$failed} failed.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
