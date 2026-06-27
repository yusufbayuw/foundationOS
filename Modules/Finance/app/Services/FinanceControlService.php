<?php

namespace Modules\Finance\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\User;
use Modules\Core\Support\NotificationService;
use Modules\Finance\Models\Budget;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Services\Support\FinanceAuditRecorder;
use Modules\Finance\Services\Support\JournalEntryBalancer;
use Modules\Finance\Services\Support\PaymentJournalPoster;
use Modules\Finance\Services\Support\StudentInvoiceRecalculator;
use RuntimeException;

class FinanceControlService
{
    public function __construct(
        private readonly FinanceAuditRecorder $auditRecorder,
        private readonly StudentInvoiceRecalculator $invoiceRecalculator,
        private readonly PaymentJournalPoster $paymentJournalPoster,
        private readonly JournalEntryBalancer $journalBalancer,
    ) {}

    public function markInvoiceIssued(StudentInvoice $invoice, User $actor, ?string $notes = null): StudentInvoice
    {
        return DB::transaction(function () use ($invoice, $actor, $notes): StudentInvoice {
            $invoice = $invoice->fresh();

            if ($invoice->isLockedForMutation()) {
                throw new RuntimeException('Issued invoice cannot be edited because it is already locked.');
            }

            $invoice->forceFill([
                'status' => 'issued',
                'notes' => $this->auditRecorder->appendNotes($invoice->notes, $notes),
            ])->save();

            $this->auditRecorder->record($invoice, $actor, 'finance_invoice_issued', [
                'status' => $invoice->status,
            ]);

            return $invoice->fresh();
        });
    }

    public function verifyPayment(Payment $payment, User $actor, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($payment, $actor, $notes): Payment {
            $payment = $payment->fresh(['studentInvoice', 'chartOfAccount']);

            if ($payment->isLockedForMutation()) {
                throw new RuntimeException('Payment is already finalized and cannot be verified again.');
            }

            $payment->forceFill([
                'status' => 'verified',
                'verified_by' => $actor->getKey(),
                'verified_at' => now(),
                'verification_notes' => $this->auditRecorder->appendNotes($payment->verification_notes, $notes),
            ])->save();

            $invoice = $this->invoiceRecalculator->recalculate($payment->studentInvoice->fresh(), $actor);
            $journal = $this->paymentJournalPoster->postForVerifiedPayment($payment->fresh(['studentInvoice', 'chartOfAccount']), $actor);

            $this->auditRecorder->record($payment, $actor, 'finance_payment_verified', [
                'invoice_id' => $invoice->getKey(),
                'journal_entry_id' => $journal->getKey(),
                'status' => $payment->status,
            ]);

            NotificationService::paymentVerified($payment->fresh(['studentInvoice']), $actor);

            return $payment->fresh(['studentInvoice', 'chartOfAccount']);
        });
    }

    public function rejectPayment(Payment $payment, User $actor, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($payment, $actor, $notes): Payment {
            $payment = $payment->fresh(['studentInvoice']);

            if ($payment->isLockedForMutation()) {
                throw new RuntimeException('Payment is already finalized and cannot be rejected.');
            }

            $payment->forceFill([
                'status' => 'rejected',
                'verified_by' => $actor->getKey(),
                'verified_at' => now(),
                'verification_notes' => $this->auditRecorder->appendNotes($payment->verification_notes, $notes),
            ])->save();

            $this->invoiceRecalculator->recalculate($payment->studentInvoice->fresh(), $actor);

            $this->auditRecorder->record($payment, $actor, 'finance_payment_rejected', [
                'status' => $payment->status,
            ]);

            NotificationService::paymentRejected($payment, $actor);

            return $payment->fresh(['studentInvoice']);
        });
    }

    public function approveBudget(Budget $budget, User $actor, ?string $notes = null): Budget
    {
        return DB::transaction(function () use ($budget, $actor, $notes): Budget {
            $budget = $budget->fresh();

            if ($budget->isLockedForMutation()) {
                throw new RuntimeException('Budget is already finalized and cannot be approved again.');
            }

            $budget->forceFill([
                'status' => 'approved',
                'approved_by' => $actor->getKey(),
                'approved_at' => now(),
                'description' => $this->auditRecorder->appendNotes($budget->description, $notes),
            ])->save();

            $this->auditRecorder->record($budget, $actor, 'finance_budget_approved', [
                'status' => $budget->status,
            ]);

            NotificationService::budgetApproved($budget, $actor);

            return $budget->fresh();
        });
    }

    public function postJournalEntry(JournalEntry $entry, User $actor, ?string $notes = null): JournalEntry
    {
        return DB::transaction(function () use ($entry, $actor, $notes): JournalEntry {
            $entry = $entry->fresh(['lines']);

            if ($entry->isLockedForMutation()) {
                throw new RuntimeException('Journal entry is already posted or reversed.');
            }

            $totals = $this->journalBalancer->totals($entry);

            if (! $this->journalBalancer->isBalanced($entry)) {
                throw new RuntimeException('Journal entry is not balanced.');
            }

            $entry->forceFill([
                'total_debit' => $totals['debit'],
                'total_credit' => $totals['credit'],
                'is_balanced' => true,
                'is_posted' => true,
                'posted_by' => $actor->getKey(),
                'posted_at' => now(),
                'description' => $notes ? trim($entry->description."\n\n".$notes) : $entry->description,
            ])->save();

            $this->auditRecorder->record($entry, $actor, 'finance_journal_posted', [
                'status' => 'posted',
                'total_debit' => $entry->total_debit,
                'total_credit' => $entry->total_credit,
            ]);

            return $entry->fresh(['lines']);
        });
    }

    public function reverseJournalEntry(JournalEntry $entry, User $actor, string $reason): JournalEntry
    {
        return DB::transaction(function () use ($entry, $actor, $reason): JournalEntry {
            $entry = $entry->fresh();

            if (! $entry->is_posted || $entry->is_reversed) {
                throw new RuntimeException('Only posted and unreversed journal entries can be reversed.');
            }

            $entry->forceFill([
                'is_reversed' => true,
                'reversal_reason' => $reason,
            ])->save();

            $this->auditRecorder->record($entry, $actor, 'finance_journal_reversed', [
                'status' => 'reversed',
                'reason' => $reason,
            ]);

            return $entry->fresh();
        });
    }

    public function recalculateInvoice(StudentInvoice $invoice, ?User $actor = null): StudentInvoice
    {
        return $this->invoiceRecalculator->recalculate($invoice, $actor);
    }
}
