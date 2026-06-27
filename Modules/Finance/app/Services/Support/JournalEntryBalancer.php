<?php

namespace Modules\Finance\Services\Support;

use Modules\Finance\Models\JournalEntry;

class JournalEntryBalancer
{
    /**
     * @return array{debit: float, credit: float}
     */
    public function totals(JournalEntry $entry): array
    {
        $debit = (float) $entry->lines()->sum('debit');
        $credit = (float) $entry->lines()->sum('credit');

        return [
            'debit' => $debit,
            'credit' => $credit,
        ];
    }

    public function isBalanced(JournalEntry $entry): bool
    {
        $totals = $this->totals($entry);

        return round($totals['debit'], 2) === round($totals['credit'], 2);
    }
}
