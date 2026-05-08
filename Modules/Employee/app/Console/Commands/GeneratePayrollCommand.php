<?php

namespace Modules\Employee\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Models\Tenant;
use Modules\Employee\Models\Employee;
use Modules\Employee\Services\PayrollCalculationService;
use Throwable;

class GeneratePayrollCommand extends Command
{
    protected $signature = 'employee:generate-payroll
                            {--tenant= : Tenant ID (required)}
                            {--month= : Month number 1-12 (default: current month)}
                            {--year= : Year (default: current year)}
                            {--employee= : Single employee ID (optional, default: all active)}
                            {--dry-run : Simulate without saving}';

    protected $description = 'Generate salary slips for all active employees in a tenant for a given period.';

    public function handle(PayrollCalculationService $service): int
    {
        $tenantId = $this->option('tenant');
        if (! $tenantId) {
            $this->error('--tenant is required.');
            return self::FAILURE;
        }

        $tenant = Tenant::find($tenantId);
        if (! $tenant) {
            $this->error("Tenant #{$tenantId} not found.");
            return self::FAILURE;
        }

        $month = (int) ($this->option('month') ?? now()->month);
        $year = (int) ($this->option('year') ?? now()->year);
        $isDryRun = (bool) $this->option('dry-run');

        if ($month < 1 || $month > 12) {
            $this->error('--month must be between 1 and 12.');
            return self::FAILURE;
        }

        $this->info("Generating payroll for: {$tenant->name} | Period: {$month}/{$year}" . ($isDryRun ? ' [DRY RUN]' : ''));

        $query = Employee::where('tenant_id', $tenantId)
            ->whereNull('end_date')
            ->orWhere('end_date', '>', now());

        if ($employeeId = $this->option('employee')) {
            $query->where('id', $employeeId);
        }

        $employees = $query->get();
        $this->info("Found {$employees->count()} active employee(s).");

        $success = 0;
        $skipped = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($employees->count());
        $bar->start();

        foreach ($employees as $employee) {
            try {
                if ($isDryRun) {
                    $this->line("\n  [DRY RUN] Would generate slip for: {$employee->full_name}");
                } else {
                    $slip = $service->generate($employee, $month, $year);
                    $this->line("\n  ✓ {$employee->full_name} → Net: Rp " . number_format($slip->net_salary, 0, ',', '.'));
                    $success++;
                }
            } catch (Throwable $e) {
                if (str_contains($e->getMessage(), 'already') && str_contains($e->getMessage(), 'cannot be regenerated')) {
                    $this->line("\n  ⚠ {$employee->full_name} → Skipped (slip already paid/cancelled)");
                    $skipped++;
                } else {
                    $this->error("\n  ✗ {$employee->full_name} → Error: {$e->getMessage()}");
                    $errors++;
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if (! $isDryRun) {
            $this->table(
                ['Result', 'Count'],
                [
                    ['Generated', $success],
                    ['Skipped (locked)', $skipped],
                    ['Errors', $errors],
                ]
            );
        }

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
