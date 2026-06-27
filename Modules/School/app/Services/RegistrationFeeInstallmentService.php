<?php

namespace Modules\School\Services;

use Carbon\Carbon;
use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Models\TuitionType;
use Modules\School\Models\Student;

class RegistrationFeeInstallmentService
{
    /**
     * @param  array{installments: int, start_month?: string}  $options
     */
    public function createInstallments(Student $student, TuitionType $tuitionType, array $options = ['installments' => 3]): int
    {
        $installments = max(1, (int) ($options['installments'] ?? 3));
        $start = Carbon::parse($options['start_month'] ?? now()->format('Y-m').'-01');
        $amountPerInstallment = round((float) $tuitionType->amount / $installments, 2);
        $created = 0;

        for ($i = 0; $i < $installments; $i++) {
            $period = $start->copy()->addMonths($i);
            if ($this->installmentExists((int) $student->tenant_id, (int) $student->id, (int) $tuitionType->id, $period, $i + 1)) {
                continue;
            }

            StudentInvoice::withoutTenantScope()->create([
                'tenant_id' => $student->tenant_id,
                'tuition_type_id' => $tuitionType->id,
                'invoiceable_type' => 'school_student',
                'invoiceable_id' => $student->id,
                'invoice_number' => 'REG-'.$student->id.'-'.$period->format('Ym').'-'.($i + 1),
                'invoice_type' => 'registration_installment',
                'issue_date' => $period->copy()->startOfMonth(),
                'due_date' => $period->copy()->day(min((int) $tuitionType->due_day, $period->daysInMonth)),
                'amount' => $amountPerInstallment,
                'discount_amount' => 0,
                'penalty_amount' => 0,
                'total_amount' => $amountPerInstallment,
                'paid_amount' => 0,
                'remaining_amount' => $amountPerInstallment,
                'status' => 'issued',
                'description' => $tuitionType->name.' — cicilan '.($i + 1).'/'.$installments,
            ]);

            $created++;
        }

        return $created;
    }

    protected function installmentExists(int $tenantId, int $studentId, int $tuitionTypeId, Carbon $period, int $sequence): bool
    {
        return StudentInvoice::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('tuition_type_id', $tuitionTypeId)
            ->where('invoiceable_type', 'school_student')
            ->where('invoiceable_id', $studentId)
            ->where('invoice_type', 'registration_installment')
            ->where('invoice_number', 'REG-'.$studentId.'-'.$period->format('Ym').'-'.$sequence)
            ->exists();
    }
}
