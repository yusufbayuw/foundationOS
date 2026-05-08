<?php

namespace Modules\Employee\Services;

use Illuminate\Support\Facades\DB;
use Modules\Employee\Enums\PayrollComponentCalculationType;
use Modules\Employee\Enums\PayrollComponentType;
use Modules\Employee\Enums\SalarySlipStatus;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\PayrollComponent;
use Modules\Employee\Models\SalarySlip;
use Modules\Employee\Models\SalarySlipComponent;
use RuntimeException;

class PayrollCalculationService
{
    public function __construct(
        private readonly AttendanceProcessingService $attendanceService,
    ) {}

    /**
     * Generate or regenerate a salary slip for an employee in a given period.
     * If a draft slip already exists for the period, it is replaced.
     * Paid/cancelled slips cannot be regenerated.
     */
    public function generate(Employee $employee, int $month, int $year): SalarySlip
    {
        return DB::transaction(function () use ($employee, $month, $year): SalarySlip {
            $existing = SalarySlip::where('employee_id', $employee->id)
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->first();

            if ($existing && $existing->isLockedForMutation()) {
                throw new RuntimeException(
                    "Salary slip for {$month}/{$year} is already {$existing->status->getLabel()} and cannot be regenerated."
                );
            }

            if ($existing) {
                $existing->components()->delete();
                $existing->delete();
            }

            $basicSalary = (float) $employee->basic_salary;
            $attendanceStats = $this->attendanceService->monthlyStats($employee->id, $month, $year);

            $components = PayrollComponent::where('tenant_id', $employee->tenant_id)
                ->where('organization_id', $employee->organization_id)
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get();

            $earningsDetails = [];
            $deductionsDetails = [];
            $totalEarnings = $basicSalary;
            $totalDeductions = 0.0;
            $taxDetails = null;
            $bpjsDetails = null;

            foreach ($components as $component) {
                $amount = $this->resolveAmount($component, $basicSalary);
                $type = $component->type instanceof PayrollComponentType
                    ? $component->type
                    : PayrollComponentType::tryFrom($component->type ?? '');

                $entry = [
                    'id' => $component->id,
                    'code' => $component->code,
                    'name' => $component->name,
                    'calculation_type' => $component->calculation_type instanceof PayrollComponentCalculationType
                        ? $component->calculation_type->value
                        : $component->calculation_type,
                    'amount' => $amount,
                    'is_taxable' => $component->is_taxable,
                ];

                if ($type === PayrollComponentType::Earning) {
                    $earningsDetails[] = $entry;
                    $totalEarnings += $amount;
                } elseif ($type === PayrollComponentType::Tax) {
                    $taxDetails = $entry;
                    $totalDeductions += $amount;
                } elseif ($type === PayrollComponentType::Bpjs) {
                    $bpjsDetails = $entry;
                    $totalDeductions += $amount;
                } else {
                    $deductionsDetails[] = $entry;
                    $totalDeductions += $amount;
                }
            }

            $netSalary = $totalEarnings - $totalDeductions;

            $slip = SalarySlip::create([
                'tenant_id' => $employee->tenant_id,
                'employee_id' => $employee->id,
                'period_month' => $month,
                'period_year' => $year,
                'period_label' => $this->periodLabel($month, $year),
                'basic_salary' => $basicSalary,
                'earnings_details' => $earningsDetails,
                'deductions_details' => $deductionsDetails,
                'total_earnings' => round($totalEarnings, 2),
                'total_deductions' => round($totalDeductions, 2),
                'net_salary' => round($netSalary, 2),
                'tax_details' => $taxDetails,
                'bpjs_details' => $bpjsDetails,
                'working_days' => $attendanceStats['working_days'],
                'working_hours' => $attendanceStats['working_hours'],
                'overtime_hours' => $attendanceStats['overtime_hours'],
                'leave_days' => $attendanceStats['leave_days'],
                'absent_days' => $attendanceStats['absent_days'],
                'status' => SalarySlipStatus::Processed,
            ]);

            // Persist components as line items for audit trail
            $this->persistComponents($slip, $employee, $basicSalary, $components);

            return $slip->fresh(['employee', 'components']);
        });
    }

    /**
     * Mark a processed salary slip as paid.
     */
    public function markPaid(SalarySlip $slip, ?string $paidVia = null): SalarySlip
    {
        return DB::transaction(function () use ($slip, $paidVia): SalarySlip {
            $slip = $slip->fresh();

            if ($slip->status !== SalarySlipStatus::Processed) {
                throw new RuntimeException('Only processed salary slips can be marked as paid.');
            }

            $slip->forceFill([
                'status' => SalarySlipStatus::Paid,
                'paid_at' => now(),
                'paid_via' => $paidVia,
            ])->save();

            return $slip->fresh();
        });
    }

    /**
     * Cancel a draft or processed salary slip.
     */
    public function cancel(SalarySlip $slip): SalarySlip
    {
        return DB::transaction(function () use ($slip): SalarySlip {
            $slip = $slip->fresh();

            if ($slip->isLockedForMutation()) {
                throw new RuntimeException('Paid salary slips cannot be cancelled.');
            }

            $slip->forceFill(['status' => SalarySlipStatus::Cancelled])->save();

            return $slip->fresh();
        });
    }

    private function resolveAmount(PayrollComponent $component, float $basicSalary): float
    {
        $calcType = $component->calculation_type instanceof PayrollComponentCalculationType
            ? $component->calculation_type
            : PayrollComponentCalculationType::tryFrom($component->calculation_type ?? '');

        return match ($calcType) {
            PayrollComponentCalculationType::Fixed => (float) $component->amount,
            PayrollComponentCalculationType::Percentage => round($basicSalary * ((float) $component->percentage / 100), 2),
            PayrollComponentCalculationType::Formula => $this->evaluateFormula($component->formula ?? '', $basicSalary),
            default => (float) ($component->amount ?? 0),
        };
    }

    /**
     * Safe formula evaluator — only allows arithmetic on {basic_salary}.
     * Supports: + - * / ( ) and the variable {basic_salary}.
     */
    private function evaluateFormula(string $formula, float $basicSalary): float
    {
        $expression = str_replace('{basic_salary}', (string) $basicSalary, $formula);

        // Strip anything that isn't a digit, operator, decimal point, or whitespace
        $safe = preg_replace('/[^0-9+\-*\/().\s]/', '', $expression);

        if (blank($safe)) {
            return 0.0;
        }

        try {
            // phpcs:ignore Squiz.PHP.Eval.Discouraged
            return (float) eval("return ({$safe});");
        } catch (\Throwable) {
            return 0.0;
        }
    }

    private function persistComponents(SalarySlip $slip, Employee $employee, float $basicSalary, $components): void
    {
        $order = 0;
        foreach ($components as $component) {
            $amount = $this->resolveAmount($component, $basicSalary);
            SalarySlipComponent::create([
                'tenant_id' => $employee->tenant_id,
                'salary_slip_id' => $slip->id,
                'payroll_component_id' => $component->id,
                'component_type' => $component->type instanceof PayrollComponentType
                    ? $component->type->value
                    : $component->type,
                'component_name' => $component->name,
                'calculation_type' => $component->calculation_type instanceof PayrollComponentCalculationType
                    ? $component->calculation_type->value
                    : $component->calculation_type,
                'amount' => round($amount, 2),
                'percentage' => $component->percentage,
                'base_amount' => $basicSalary,
                'formula_used' => $component->formula,
                'is_taxable' => $component->is_taxable,
                'is_mandatory' => $component->is_mandatory,
                'display_order' => $order++,
            ]);
        }
    }

    private function periodLabel(int $month, int $year): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return ($months[$month] ?? $month) . ' ' . $year;
    }
}
