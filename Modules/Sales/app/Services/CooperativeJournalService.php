<?php

namespace Modules\Sales\Services;

use Modules\Core\Services\RevenueJournalHelper;
use Modules\Finance\Models\JournalEntry;
use Modules\Sales\Models\CooperativeSaving;
use RuntimeException;

class CooperativeJournalService
{
    public function __construct(
        private readonly RevenueJournalHelper $journalHelper,
    ) {}

    public function postSavings(CooperativeSaving $saving): JournalEntry
    {
        if ($saving->journal_entry_id) {
            return JournalEntry::query()->findOrFail($saving->journal_entry_id);
        }

        $amount = (float) $saving->amount;
        $cash = $this->journalHelper->findCoa($saving->tenant_id, 'asset', ['kas', 'bank']);
        $equity = $this->journalHelper->findCoa($saving->tenant_id, 'equity', ['simpanan', 'koperasi', 'modal']);

        if (! $cash || ! $equity) {
            throw new RuntimeException('COA for cooperative savings journal not configured.');
        }

        $entry = $this->journalHelper->postIfMissing(
            tenantId: $saving->tenant_id,
            entryNumber: 'COOP-SAV-'.$saving->getKey(),
            description: 'Cooperative savings '.$saving->savings_type,
            lines: [
                ['chart_of_account_id' => $cash->id, 'debit' => $amount, 'credit' => 0],
                ['chart_of_account_id' => $equity->id, 'debit' => 0, 'credit' => $amount],
            ],
            date: $saving->transaction_date,
        );

        $saving->forceFill(['journal_entry_id' => $entry->id])->save();

        return $entry;
    }
}
