<?php

namespace Modules\Finance\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\Core\Support\NotificationService;
use Modules\Finance\Models\Budget;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;
use Modules\Monitoring\Models\AuditLog;
use RuntimeException;

class FinanceControlService
{
    public function markInvoiceIssued(StudentInvoice $invoice, User $actor, ?string $notes = null): StudentInvoice
    {
        return DB::transaction(function () use ($invoice, $actor, $notes): StudentInvoice {
            $invoice = $invoice->fresh();

            if ($invoice->isLockedForMutation()) {
                throw new RuntimeException('Issued invoice cannot be edited because it is already locked.');
            }

            $invoice->forceFill([
                'status' => 'issued',
                'notes' => $this->appendNotes($invoice->notes, $notes),
            ])->save();

            $this->audit($invoice, $actor, 'finance_invoice_issued', [
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
                'verification_notes' => $this->appendNotes($payment->verification_notes, $notes),
            ])->save();

            $invoice = $this->recalculateInvoice($payment->studentInvoice->fresh());
            $journal = $this->createPaymentJournalEntry($payment->fresh(['studentInvoice', 'chartOfAccount']), $actor);

            $this->audit($payment, $actor, 'finance_payment_verified', [
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
                'verification_notes' => $this->appendNotes($payment->verification_notes, $notes),
            ])->save();

            $this->recalculateInvoice($payment->studentInvoice->fresh());

            $this->audit($payment, $actor, 'finance_payment_rejected', [
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
                'description' => $this->appendNotes($budget->description, $notes),
            ])->save();

            $this->audit($budget, $actor, 'finance_budget_approved', [
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

            $totals = $this->calculateJournalTotals($entry);

            if (round((float) $totals['debit'], 2) !== round((float) $totals['credit'], 2)) {
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

            $this->audit($entry, $actor, 'finance_journal_posted', [
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

            $this->audit($entry, $actor, 'finance_journal_reversed', [
                'status' => 'reversed',
                'reason' => $reason,
            ]);

            return $entry->fresh();
        });
    }

    public function recalculateInvoice(StudentInvoice $invoice): StudentInvoice
    {
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

        return $invoice->fresh();
    }

    protected function createPaymentJournalEntry(Payment $payment, User $actor): JournalEntry
    {
        $invoice = $payment->studentInvoice;
        $cashAccount = $payment->chartOfAccount;
        $receivableAccount = $this->resolveReceivableAccount($payment);

        $existing = JournalEntry::query()
            ->where('tenant_id', $payment->tenant_id)
            ->where('entry_number', 'PAY-'.$payment->payment_number)
            ->first();

        if ($existing) {
            return $existing;
        }

        $entry = JournalEntry::query()->create([
            'tenant_id' => $payment->tenant_id,
            'organization_id' => $invoice->invoiceable?->organization_id ?? $cashAccount->organization_id,
            'posted_by' => $actor->getKey(),
            'entry_number' => 'PAY-'.$payment->payment_number,
            'date' => $payment->payment_date,
            'description' => 'Auto journal for verified payment '.$payment->payment_number,
            'total_debit' => $payment->amount,
            'total_credit' => $payment->amount,
            'is_balanced' => true,
            'is_posted' => true,
            'posted_at' => now(),
            'is_reversed' => false,
        ]);

        JournalEntryLine::query()->create([
            'tenant_id' => $payment->tenant_id,
            'journal_entry_id' => $entry->id,
            'chart_of_account_id' => $cashAccount->id,
            'description' => 'Cash / bank receipt for payment '.$payment->payment_number,
            'debit' => $payment->amount,
            'credit' => 0,
        ]);

        JournalEntryLine::query()->create([
            'tenant_id' => $payment->tenant_id,
            'journal_entry_id' => $entry->id,
            'chart_of_account_id' => $receivableAccount->id,
            'description' => 'Accounts receivable settlement for invoice '.$invoice->invoice_number,
            'debit' => 0,
            'credit' => $payment->amount,
        ]);

        return $entry->fresh(['lines']);
    }

    protected function resolveReceivableAccount(Payment $payment): ChartOfAccount
    {
        $setting = TenantSetting::query()
            ->where('tenant_id', $payment->tenant_id)
            ->where('group', 'finance')
            ->where('key', 'default_receivable_account_id')
            ->first();

        $accountId = (int) ($setting?->value ?? 0);
        $account = ChartOfAccount::query()
            ->where('tenant_id', $payment->tenant_id)
            ->find($accountId);

        if (! $account) {
            throw new RuntimeException('Finance default receivable account is not configured in tenant_settings (group=finance, key=default_receivable_account_id).');
        }

        return $account;
    }

    protected function calculateJournalTotals(JournalEntry $entry): array
    {
        $debit = (float) $entry->lines()->sum('debit');
        $credit = (float) $entry->lines()->sum('credit');

        return [
            'debit' => $debit,
            'credit' => $credit,
        ];
    }

    protected function audit(object $record, User $actor, string $action, array $newValues = []): void
    {
        AuditLog::query()->create([
            'tenant_id' => $record->tenant_id,
            'organization_id' => $record->organization_id ?? null,
            'user_id' => $actor->getKey(),
            'auditable_type' => $record::class,
            'auditable_id' => $record->getKey(),
            'action' => $action,
            'description' => str($action)->headline()->toString(),
            'old_values' => null,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_id' => request()?->headers->get('X-Request-Id'),
            'status' => 'success',
            'error_message' => null,
        ]);
    }

    protected function appendNotes(?string $existing, ?string $incoming): ?string
    {
        return trim(implode("\n\n", array_filter([$existing, $incoming]))) ?: null;
    }
}
