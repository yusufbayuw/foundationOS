<?php

namespace Modules\Employee\Services;

use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\SalarySlip;
use Modules\Employee\Models\SalarySlipComponent;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;
use Modules\Monitoring\Services\AuditTrailRecorder;
use RuntimeException;

/**
 * Posts a journal entry when a salary slip is marked as paid.
 *
 * This intentionally remains synchronous because it posts one salary slip into
 * one bounded journal entry: a salary expense line, optional deduction lines,
 * and a cash/bank line. The payment transaction should fail immediately if
 * required accounts are missing.
 *
 * Journal pattern (per Indonesian PSAK):
 *   Dr  Beban Gaji (Expense)        = total_earnings
 *   Cr  Hutang BPJS / Pajak (Liab)  = each deduction component with coa_id
 *   Cr  Kas / Bank (Asset)          = net_salary
 *
 * Missing deduction account mappings are rejected rather than silently
 * overstating the cash payout; a posted entry is committed with the payment.
 */
class PayrollJournalService
{
    public function postForSlip(SalarySlip $slip): JournalEntry
    {
        return DB::transaction(function () use ($slip): JournalEntry {
            $slip = SalarySlip::query()
                ->whereKey($slip->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($slip->journal_entry_id) {
                $journal = $slip->journalEntry;

                if (! $journal || ! $journal->is_posted || $journal->is_reversed) {
                    throw new RuntimeException('Existing payroll journal requires finance reconciliation before retry.');
                }

                return $journal;
            }

            if ($slip->status !== 'paid') {
                throw new RuntimeException('Only paid salary slips can create a payroll journal.');
            }

            $slip->load(['employee', 'components.payrollComponent']);

            if (! $slip->employee || (int) $slip->employee->tenant_id !== (int) $slip->tenant_id) {
                throw new RuntimeException('Payroll employee is not associated with this tenant.');
            }

            $organizationId = (int) $slip->employee->organization_id;
            $earningsCents = (int) round((float) $slip->total_earnings * 100);
            $deductionsCents = (int) round((float) $slip->total_deductions * 100);
            $netCents = (int) round((float) $slip->net_salary * 100);

            if ($earningsCents <= 0 || $deductionsCents < 0 || $netCents < 0 || $earningsCents !== $deductionsCents + $netCents) {
                throw new RuntimeException('Payroll totals do not reconcile: earnings must equal deductions plus net salary.');
            }

            $expenseAccount = $this->findRequiredCoa(
                $slip->tenant_id,
                $organizationId,
                'expense',
                ['beban gaji', 'salary expense', 'beban upah'],
                'Payroll journal requires an active salary expense account.',
            );

            $cashAccount = $this->findRequiredCoa(
                $slip->tenant_id,
                $organizationId,
                'asset',
                ['kas', 'bank', 'cash'],
                'Payroll journal requires an active cash or bank account.',
            );

            $deductionLines = [];
            $mappedDeductionsCents = 0;

            foreach ($slip->components->where('component_type', 'deduction') as $component) {
                $amountCents = (int) round((float) $component->amount * 100);

                if ($amountCents < 0) {
                    throw new RuntimeException('Payroll deduction amounts cannot be negative.');
                }

                if ($amountCents === 0) {
                    continue;
                }

                $accountId = $component->payrollComponent?->chart_of_account_id;
                $account = ChartOfAccount::withoutTenantScope()
                    ->where('tenant_id', $slip->tenant_id)
                    ->where('organization_id', $organizationId)
                    ->where('is_active', true)
                    ->find($accountId);

                if (! $account) {
                    throw new RuntimeException('Payroll deduction '.$component->component_name.' requires an active account in the employee organization.');
                }

                $deductionLines[] = [
                    'tenant_id' => $slip->tenant_id,
                    'chart_of_account_id' => $account->id,
                    'description' => $component->component_name.' — '.$slip->employee->full_name,
                    'debit' => 0,
                    'credit' => $amountCents / 100,
                ];
                $mappedDeductionsCents += $amountCents;
            }

            if ($mappedDeductionsCents !== $deductionsCents) {
                throw new RuntimeException('Payroll component deductions do not reconcile to salary slip totals.');
            }

            $entryNumber = 'JE-PAYROLL-'
                .str_pad((string) $slip->period_month, 2, '0', STR_PAD_LEFT)
                .'-'.$slip->period_year
                .'-EMP'.$slip->employee_id;

            $journal = JournalEntry::query()->create([
                'tenant_id' => $slip->tenant_id,
                'organization_id' => $organizationId,
                'entry_number' => $entryNumber,
                'date' => $slip->paid_at?->toDateString() ?? now()->toDateString(),
                'description' => sprintf('Payroll %s — %s', $slip->period_label, $slip->employee->full_name),
                'total_debit' => $earningsCents / 100,
                'total_credit' => $earningsCents / 100,
                'is_balanced' => true,
                'is_posted' => false,
            ]);

            JournalEntryLine::query()->create([
                'tenant_id' => $slip->tenant_id,
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => $expenseAccount->id,
                'description' => 'Beban Gaji — '.$slip->employee->full_name,
                'debit' => $earningsCents / 100,
                'credit' => 0,
            ]);

            foreach ($deductionLines as $line) {
                $journal->lines()->create($line);
            }

            JournalEntryLine::query()->create([
                'tenant_id' => $slip->tenant_id,
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => $cashAccount->id,
                'description' => 'Kas/Bank Pembayaran Gaji — '.$slip->employee->full_name,
                'debit' => 0,
                'credit' => $netCents / 100,
            ]);

            if ($earningsCents !== $mappedDeductionsCents + $netCents) {
                throw new RuntimeException('Payroll journal is not balanced.');
            }

            $journal->forceFill([
                'is_posted' => true,
                'posted_at' => now(),
                'posted_by' => auth()->id(),
            ])->save();

            $slip->forceFill(['journal_entry_id' => $journal->id])->save();

            AuditTrailRecorder::record($journal, 'payroll_journal_posted', null, [
                'salary_slip_id' => $slip->getKey(),
                'total_debit' => $journal->total_debit,
                'total_credit' => $journal->total_credit,
            ]);

            return $journal->fresh(['lines']);
        }, 3);
    }

    /**
     * @param  array<int, string>  $keywords
     */
    private function findRequiredCoa(int $tenantId, int $organizationId, string $type, array $keywords, string $message): ChartOfAccount
    {
        $account = ChartOfAccount::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('organization_id', $organizationId)
            ->where('type', $type)
            ->where('is_active', true)
            ->where(function ($query) use ($keywords): void {
                foreach ($keywords as $keyword) {
                    $query->orWhereRaw('LOWER(name) LIKE ?', ['%'.strtolower($keyword).'%']);
                }
            })
            ->first();

        if (! $account) {
            throw new RuntimeException($message);
        }

        return $account;
    }
}
