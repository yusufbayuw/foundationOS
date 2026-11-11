<?php

namespace Modules\Employee\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Employee\Models\SalarySlip;
use Modules\Finance\Models\JournalEntry;

class SalarySlipPaymentService
{
    public function __construct(private readonly PayrollJournalService $payrollJournalService) {}

    public function markAsPaid(SalarySlip $slip, CarbonInterface|string $paidAt, string $paidVia): JournalEntry
    {
        return DB::transaction(function () use ($slip, $paidAt, $paidVia): JournalEntry {
            /** @var SalarySlip $lockedSlip */
            $lockedSlip = SalarySlip::query()
                ->whereKey($slip->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedSlip->status === 'paid') {
                // Retry safely without changing the original payment timestamp.
                return $this->payrollJournalService->postForSlip($lockedSlip);
            }

            if ($lockedSlip->status !== 'approved') {
                throw new InvalidArgumentException('Only approved salary slips can be marked as paid.');
            }

            $lockedSlip->forceFill([
                'status' => 'paid',
                'paid_at' => $paidAt,
                'paid_via' => $paidVia,
            ])->save();

            return $this->payrollJournalService->postForSlip($lockedSlip->fresh());
        });
    }
}
