<?php

namespace Modules\Employee\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Organization;
use Modules\Employee\Models\SalarySlip;
use Modules\Employee\Models\SalarySlipComponent;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;

/**
 * Posts a journal entry when a salary slip is marked as paid.
 *
 * Journal pattern (per Indonesian PSAK):
 *   Dr  Beban Gaji (Expense)        = total_earnings
 *   Cr  Hutang BPJS / Pajak (Liab)  = each deduction component with coa_id
 *   Cr  Kas / Bank (Asset)          = net_salary
 *
 * If a component has no chart_of_account_id, its amount is absorbed into the
 * default cash/bank credit line, keeping debit = credit.
 */
class PayrollJournalService
{
    public function postForSlip(SalarySlip $slip): JournalEntry
    {
        if ($slip->journal_entry_id) {
            return $slip->journalEntry;
        }

        return DB::transaction(function () use ($slip): JournalEntry {
            $slip->load(['employee', 'components.payrollComponent']);

            $organizationId = Organization::withoutTenantScope()
                ->where('tenant_id', $slip->tenant_id)
                ->value('id');

            $entryNumber = 'JE-PAYROLL-'
                .str_pad((string) $slip->period_month, 2, '0', STR_PAD_LEFT)
                .'-'.$slip->period_year
                .'-EMP'.$slip->employee_id;

            $journal = JournalEntry::query()->create([
                'tenant_id' => $slip->tenant_id,
                'organization_id' => $organizationId,
                'entry_number' => $entryNumber,
                'date' => $slip->paid_at?->toDateString() ?? now()->toDateString(),
                'description' => sprintf(
                    'Payroll %s — %s',
                    $slip->period_label,
                    $slip->employee->full_name,
                ),
                'total_debit' => $slip->total_earnings,
                'total_credit' => $slip->total_earnings,
                'is_balanced' => true,
                'is_posted' => false,
            ]);

            // Debit: Salary Expense = total_earnings
            $salaryExpenseCoa = $this->findOrNullCoa($slip->tenant_id, 'expense', ['beban gaji', 'salary expense', 'beban upah']);

            JournalEntryLine::query()->create([
                'tenant_id' => $slip->tenant_id,
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => $salaryExpenseCoa?->id,
                'description' => 'Beban Gaji — '.$slip->employee->full_name,
                'debit' => $slip->total_earnings,
                'credit' => 0,
            ]);

            // Credit: per-component deductions that have a dedicated CoA
            $netCredit = (float) $slip->total_earnings;
            $deductions = $slip->components
                ->where('component_type', 'deduction')
                ->filter(fn (SalarySlipComponent $c) => $c->payrollComponent?->chart_of_account_id !== null);

            foreach ($deductions as $component) {
                JournalEntryLine::query()->create([
                    'tenant_id' => $slip->tenant_id,
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => $component->payrollComponent->chart_of_account_id,
                    'description' => $component->component_name.' — '.$slip->employee->full_name,
                    'debit' => 0,
                    'credit' => $component->amount,
                ]);
                $netCredit -= (float) $component->amount;
            }

            // Credit: remaining net salary → Cash/Bank
            $cashCoa = $this->findOrNullCoa($slip->tenant_id, 'asset', ['kas', 'bank', 'cash']);

            JournalEntryLine::query()->create([
                'tenant_id' => $slip->tenant_id,
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => $cashCoa?->id,
                'description' => 'Kas/Bank Pembayaran Gaji — '.$slip->employee->full_name,
                'debit' => 0,
                'credit' => max(0, $netCredit),
            ]);

            $slip->forceFill(['journal_entry_id' => $journal->id])->save();

            return $journal;
        });
    }

    private function findOrNullCoa(int $tenantId, string $type, array $keywords): ?ChartOfAccount
    {
        $query = ChartOfAccount::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('type', $type)
            ->where('is_active', true);

        foreach ($keywords as $kw) {
            $query->orWhere(function ($q) use ($tenantId, $type, $kw): void {
                $q->where('tenant_id', $tenantId)
                    ->where('type', $type)
                    ->where('is_active', true)
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($kw).'%']);
            });
        }

        return $query->first();
    }
}
