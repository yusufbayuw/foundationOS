<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\SalarySlip;

class SalarySlipImporter extends BaseModelImporter
{
    protected static ?string $model = SalarySlip::class;

    public static function getColumns(): array
    {
        // Only draft payroll descriptors may be imported. Approval, payment,
        // totals and journal links are exclusively created by payroll services.
        return [
            ImportColumn::make('employee_id')->numeric(),
            ImportColumn::make('period_month')->numeric(),
            ImportColumn::make('period_year')->numeric(),
            ImportColumn::make('notes'),
        ];
    }

    protected function beforeSave(): void
    {
        parent::beforeSave();

        $record = $this->getRecord();

        if (! $record instanceof SalarySlip) {
            return;
        }

        $month = (int) $record->period_month;
        $year = (int) $record->period_year;

        if ($month < 1 || $month > 12 || $year < 1900 || $year > 2100) {
            throw new RowImportFailedException('Invalid payroll period.');
        }

        $employee = Employee::withoutTenantScope()
            ->where('tenant_id', $record->tenant_id)
            ->find($record->employee_id);

        if (! $employee) {
            throw new RowImportFailedException('Employee must belong to the importing tenant.');
        }

        if (SalarySlip::withoutTenantScope()
            ->where('tenant_id', $record->tenant_id)
            ->where('employee_id', $employee->id)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->exists()) {
            throw new RowImportFailedException('Salary slip already exists for this employee and period.');
        }

        $basicSalary = (float) $employee->basic_salary;

        $record->forceFill([
            'period_label' => date('F Y', mktime(0, 0, 0, $month, 1, $year)),
            'basic_salary' => $basicSalary,
            'earnings_details' => [],
            'deductions_details' => [],
            'total_earnings' => $basicSalary,
            'total_deductions' => 0,
            'net_salary' => $basicSalary,
            'working_days' => 0,
            'working_hours' => 0,
            'overtime_hours' => 0,
            'leave_days' => 0,
            'absent_days' => 0,
            'status' => 'draft',
            'paid_at' => null,
            'paid_via' => null,
            'journal_entry_id' => null,
            'is_sent' => false,
            'sent_at' => null,
        ]);
    }
}
