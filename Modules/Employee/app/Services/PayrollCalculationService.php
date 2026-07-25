<?php

namespace Modules\Employee\Services;

use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\AttendanceLog;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\PayrollComponent;
use Modules\Employee\Models\SalarySlip;
use Modules\Employee\Models\SalarySlipComponent;

/**
 * Calculates payroll for one employee in a given period.
 *
 * This intentionally remains synchronous because it processes one employee
 * slip, aggregates one attendance range, and writes a bounded set of payroll
 * component lines for immediate Filament feedback.
 *
 * Component calculation_type:
 *   fixed      → use component.amount directly
 *   percentage → (component.percentage / 100) × basic_salary
 *   formula    → evaluate expression with {variable} substitution
 *
 * Available formula variables: basic_salary, working_days, absent_days,
 * overtime_hours, leave_days, work_hours.
 */
class PayrollCalculationService
{
    public function calculate(
        Employee $employee,
        int $month,
        int $year,
        bool $dryRun = false,
    ): SalarySlip {
        $attendanceStats = $this->aggregateAttendance($employee, $month, $year);

        return DB::transaction(function () use ($employee, $month, $year, $attendanceStats, $dryRun): SalarySlip {
            $slip = $this->resolveSlip($employee, $month, $year, $attendanceStats, $dryRun);

            $components = PayrollComponent::query()
                ->where('tenant_id', $employee->tenant_id)
                ->where('is_active', true)
                ->where(function ($q) use ($employee): void {
                    $q->whereNull('organization_id')
                        ->orWhere('organization_id', $employee->organization_id);
                })
                ->orderBy('display_order')
                ->get();

            if (! $dryRun) {
                SalarySlipComponent::query()
                    ->where('salary_slip_id', $slip->id)
                    ->delete();
            }

            $earnings = [];
            $deductions = [];
            $totalEarnings = (float) $employee->basic_salary;
            $totalDeductions = 0.0;

            $vars = [
                'basic_salary' => (float) $employee->basic_salary,
                'working_days' => $attendanceStats['working_days'],
                'absent_days' => $attendanceStats['absent_days'],
                'overtime_hours' => $attendanceStats['overtime_hours'],
                'leave_days' => $attendanceStats['leave_days'],
                'work_hours' => $attendanceStats['work_hours'],
            ];

            foreach ($components as $component) {
                $amount = $this->resolveAmount($component, $vars);
                $line = [
                    'tenant_id' => $employee->tenant_id,
                    'salary_slip_id' => $slip->id,
                    'payroll_component_id' => $component->id,
                    'component_type' => $component->type,
                    'component_name' => $component->name,
                    'calculation_type' => $component->calculation_type,
                    'amount' => $amount,
                    'percentage' => $component->percentage,
                    'base_amount' => (float) $employee->basic_salary,
                    'formula_used' => $component->formula,
                    'is_taxable' => $component->is_taxable,
                    'is_mandatory' => $component->is_mandatory,
                    'display_order' => $component->display_order ?? 0,
                ];

                if (! $dryRun) {
                    SalarySlipComponent::query()->create($line);
                }

                if ($component->type === 'earning') {
                    $earnings[] = ['name' => $component->name, 'amount' => $amount];
                    $totalEarnings += $amount;
                } else {
                    $deductions[] = ['name' => $component->name, 'amount' => $amount];
                    $totalDeductions += $amount;
                }
            }

            $netSalary = $totalEarnings - $totalDeductions;

            $slip->forceFill([
                'earnings_details' => $earnings,
                'deductions_details' => $deductions,
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'working_days' => $attendanceStats['working_days'],
                'working_hours' => $attendanceStats['work_hours'],
                'overtime_hours' => $attendanceStats['overtime_hours'],
                'leave_days' => $attendanceStats['leave_days'],
                'absent_days' => $attendanceStats['absent_days'],
                'status' => $dryRun ? $slip->status : 'draft',
            ]);

            if (! $dryRun) {
                $slip->save();
            }

            return $slip;
        });
    }

    /**
     * @param  array{working_days:int, absent_days:int, overtime_hours:float, leave_days:int, work_hours:float}  $stats
     */
    private function resolveSlip(Employee $employee, int $month, int $year, array $stats, bool $dryRun): SalarySlip
    {
        $periodLabel = date('F Y', mktime(0, 0, 0, $month, 1, $year));
        $basicSalary = (float) $employee->basic_salary;

        if ($dryRun) {
            return new SalarySlip([
                'tenant_id' => $employee->tenant_id,
                'employee_id' => $employee->id,
                'period_month' => $month,
                'period_year' => $year,
                'period_label' => $periodLabel,
                'basic_salary' => $basicSalary,
                'earnings_details' => [],
                'deductions_details' => [],
                'total_earnings' => $basicSalary,
                'total_deductions' => 0,
                'net_salary' => $basicSalary,
                'working_days' => $stats['working_days'],
                'working_hours' => $stats['work_hours'],
                'overtime_hours' => $stats['overtime_hours'],
                'leave_days' => $stats['leave_days'],
                'absent_days' => $stats['absent_days'],
                'status' => 'draft',
            ]);
        }

        return SalarySlip::query()->firstOrCreate(
            [
                'tenant_id' => $employee->tenant_id,
                'employee_id' => $employee->id,
                'period_month' => $month,
                'period_year' => $year,
            ],
            [
                'period_label' => $periodLabel,
                'basic_salary' => $basicSalary,
                'earnings_details' => [],
                'deductions_details' => [],
                'total_earnings' => $basicSalary,
                'total_deductions' => 0,
                'net_salary' => $basicSalary,
                'working_days' => $stats['working_days'],
                'working_hours' => $stats['work_hours'],
                'overtime_hours' => $stats['overtime_hours'],
                'leave_days' => $stats['leave_days'],
                'absent_days' => $stats['absent_days'],
                'status' => 'draft',
            ],
        );
    }

    private function aggregateAttendance(Employee $employee, int $month, int $year): array
    {
        $logs = AttendanceLog::query()
            ->where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        return [
            'working_days' => $logs->whereIn('status', ['present', 'late'])->count(),
            'absent_days' => $logs->where('status', 'absent')->count(),
            'leave_days' => $logs->where('status', 'leave')->count(),
            'overtime_hours' => (float) $logs->sum('overtime_hours'),
            'work_hours' => (float) $logs->sum('work_hours'),
        ];
    }

    private function resolveAmount(PayrollComponent $component, array $vars): float
    {
        return match ($component->calculation_type) {
            'fixed' => (float) $component->amount,
            'percentage' => round((float) $component->percentage / 100 * $vars['basic_salary'], 2),
            'formula' => $this->evaluateFormula((string) $component->formula, $vars),
            default => (float) $component->amount,
        };
    }

    private function evaluateFormula(string $formula, array $vars): float
    {
        // Replace {variable} placeholders with their numeric values
        $expression = preg_replace_callback(
            '/\{(\w+)\}/',
            fn (array $m) => isset($vars[$m[1]]) ? (string) $vars[$m[1]] : '0',
            $formula,
        );

        // Allow only safe characters: digits, operators, whitespace, parens, dot
        if (! preg_match('/^[\d\s\+\-\*\/\(\)\.]+$/', $expression)) {
            return 0.0;
        }

        try {
            return (float) eval("return ($expression);");
        } catch (\Throwable) {
            return 0.0;
        }
    }
}
