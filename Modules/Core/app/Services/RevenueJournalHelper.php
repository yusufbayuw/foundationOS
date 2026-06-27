<?php

namespace Modules\Core\Services;

use App\Support\TypedValue;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Organization;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;
use RuntimeException;

/**
 * Idempotent balanced journal helper for revenue-engine modules.
 */
class RevenueJournalHelper
{
    /**
     * @param  array<int, array{chart_of_account_id: int, debit: float, credit: float, description?: string}>  $lines
     */
    public function postIfMissing(
        int $tenantId,
        string $entryNumber,
        string $description,
        array $lines,
        ?int $organizationId = null,
        ?\DateTimeInterface $date = null,
    ): JournalEntry {
        $existing = JournalEntry::query()
            ->where('tenant_id', $tenantId)
            ->where('entry_number', $entryNumber)
            ->first();

        if ($existing) {
            return $existing;
        }

        $organizationId ??= Organization::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->orderBy('id')
            ->value('id');

        if ($organizationId === null) {
            throw new RuntimeException('No organization found for tenant journal posting.');
        }

        $totalDebit = round(array_sum(array_column($lines, 'debit')), 2);
        $totalCredit = round(array_sum(array_column($lines, 'credit')), 2);

        if ($totalDebit !== $totalCredit) {
            throw new RuntimeException("Journal {$entryNumber} is not balanced: debit={$totalDebit} credit={$totalCredit}");
        }

        return DB::transaction(function () use ($tenantId, $organizationId, $entryNumber, $description, $lines, $totalDebit, $totalCredit, $date): JournalEntry {
            $entry = JournalEntry::query()->create([
                'tenant_id' => $tenantId,
                'organization_id' => $organizationId,
                'entry_number' => $entryNumber,
                'date' => ($date ?? now())->format('Y-m-d'),
                'description' => $description,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'is_balanced' => true,
                'is_posted' => false,
            ]);

            foreach ($lines as $line) {
                JournalEntryLine::query()->create([
                    'tenant_id' => $tenantId,
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $line['chart_of_account_id'],
                    'description' => $line['description'] ?? $description,
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                ]);
            }

            return TypedValue::model($entry->fresh(['lines']));
        });
    }

    /**
     * @param  array<int, string>  $nameHints
     */
    public function findCoa(int $tenantId, string $type, array $nameHints): ?ChartOfAccount
    {
        $query = ChartOfAccount::query()
            ->where('tenant_id', $tenantId)
            ->where('type', $type)
            ->where('is_active', true);

        foreach ($nameHints as $hint) {
            $match = (clone $query)->where('name', 'like', '%'.$hint.'%')->first();
            if ($match) {
                return $match;
            }
        }

        return $query->orderBy('id')->first();
    }
}
