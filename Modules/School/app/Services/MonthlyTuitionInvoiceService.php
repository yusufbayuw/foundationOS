<?php

namespace Modules\School\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Models\TuitionType;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Student;

class MonthlyTuitionInvoiceService
{
    public function generateForTenant(int $tenantId, string $month): int
    {
        $period = Carbon::parse($month.'-01');
        $tuitionTypes = TuitionType::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('frequency', 'monthly')
            ->get();

        $created = 0;
        foreach ($tuitionTypes as $tuitionType) {
            $students = $this->activeStudentsForTuition($tenantId, $tuitionType->education_level);
            foreach ($students as $student) {
                if ($this->invoiceExists($tenantId, $student->id, $tuitionType->id, $period)) {
                    continue;
                }

                $amount = (float) $tuitionType->amount;
                $dueDate = $period->copy()->day(min((int) $tuitionType->due_day, $period->daysInMonth));

                StudentInvoice::withoutTenantScope()->create([
                    'tenant_id' => $tenantId,
                    'tuition_type_id' => $tuitionType->id,
                    'invoiceable_type' => 'school_student',
                    'invoiceable_id' => $student->id,
                    'invoice_number' => $this->nextInvoiceNumber($tenantId, $period),
                    'invoice_type' => 'tuition',
                    'issue_date' => $period->copy()->startOfMonth(),
                    'due_date' => $dueDate,
                    'amount' => $amount,
                    'discount_amount' => 0,
                    'penalty_amount' => 0,
                    'total_amount' => $amount,
                    'paid_amount' => 0,
                    'remaining_amount' => $amount,
                    'status' => 'issued',
                    'description' => $tuitionType->name.' — '.$period->translatedFormat('F Y'),
                ]);

                $created++;
            }
        }

        return $created;
    }

    /**
     * @return Collection<int, Student>
     */
    protected function activeStudentsForTuition(int $tenantId, ?string $educationLevel)
    {
        $activeStudentIds = ClassStudent::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->pluck('student_id');

        $query = Student::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->whereIn('id', $activeStudentIds)
            ->where('status', 'active');

        return $query->get();
    }

    protected function invoiceExists(int $tenantId, int $studentId, int $tuitionTypeId, Carbon $period): bool
    {
        return StudentInvoice::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('tuition_type_id', $tuitionTypeId)
            ->where('invoiceable_type', 'school_student')
            ->where('invoiceable_id', $studentId)
            ->whereYear('issue_date', $period->year)
            ->whereMonth('issue_date', $period->month)
            ->exists();
    }

    protected function nextInvoiceNumber(int $tenantId, Carbon $period): string
    {
        $prefix = 'INV-'.$period->format('Ym').'-';

        $last = StudentInvoice::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('invoice_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('invoice_number');

        $seq = 1;
        if ($last && preg_match('/-(\d+)$/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
