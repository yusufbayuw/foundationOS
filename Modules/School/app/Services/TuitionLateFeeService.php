<?php

namespace Modules\School\Services;

use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Models\TuitionType;

class TuitionLateFeeService
{
    public function applyForTenant(int $tenantId): int
    {
        $today = now()->toDateString();
        $updated = 0;

        $invoices = StudentInvoice::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['issued', 'partial'])
            ->whereDate('due_date', '<', $today)
            ->where('penalty_amount', 0)
            ->with('tuitionType')
            ->get();

        foreach ($invoices as $invoice) {
            $tuitionType = $invoice->tuitionType;
            if (! $tuitionType) {
                continue;
            }

            $graceDays = (int) ($tuitionType->grace_period_days ?? 0);
            if (now()->diffInDays($invoice->due_date, false) > -$graceDays) {
                continue;
            }

            $lateFee = $this->calculateLateFee($invoice, $tuitionType);
            if ($lateFee <= 0) {
                continue;
            }

            $invoice->penalty_amount = $lateFee;
            $invoice->total_amount = (float) $invoice->amount - (float) $invoice->discount_amount + $lateFee;
            $invoice->remaining_amount = (float) $invoice->total_amount - (float) $invoice->paid_amount;
            $invoice->save();
            $updated++;
        }

        return $updated;
    }

    protected function calculateLateFee(StudentInvoice $invoice, TuitionType $tuitionType): float
    {
        $fixed = (float) ($tuitionType->late_fee_fixed ?? 0);
        $percent = (float) ($tuitionType->late_fee_percentage ?? 0);
        $percentFee = $percent > 0 ? ((float) $invoice->amount * $percent / 100) : 0;

        return max($fixed, $percentFee);
    }
}
