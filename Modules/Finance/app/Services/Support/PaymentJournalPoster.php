<?php

namespace Modules\Finance\Services\Support;

use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;
use Modules\Finance\Models\Payment;
use RuntimeException;

class PaymentJournalPoster
{
    public function postForVerifiedPayment(Payment $payment, User $actor): JournalEntry
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
}
