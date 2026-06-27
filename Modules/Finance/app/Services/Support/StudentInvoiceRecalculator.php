<?php

namespace Modules\Finance\Services\Support;

use Modules\Core\Models\User;
use Modules\Finance\Events\StudentInvoicePaid;
use Modules\Finance\Models\StudentInvoice;

class StudentInvoiceRecalculator
{
    public function recalculate(StudentInvoice $invoice, ?User $actor = null): StudentInvoice
    {
        $previousStatus = $invoice->status;

        $verifiedTotal = (float) $invoice->payments()
            ->where('status', 'verified')
            ->sum('amount');

        $remaining = max(0, (float) $invoice->total_amount - $verifiedTotal);

        $status = match (true) {
            $verifiedTotal <= 0 && $invoice->status === 'draft' => 'draft',
            $verifiedTotal <= 0 => 'issued',
            $remaining > 0 => 'partial',
            default => 'paid',
        };

        $invoice->forceFill([
            'paid_amount' => $verifiedTotal,
            'remaining_amount' => $remaining,
            'status' => $status,
        ])->save();

        $invoice = $invoice->fresh();

        if ($invoice->status === 'paid' && $previousStatus !== 'paid') {
            StudentInvoicePaid::dispatch($invoice, $actor);
        }

        return $invoice;
    }
}
