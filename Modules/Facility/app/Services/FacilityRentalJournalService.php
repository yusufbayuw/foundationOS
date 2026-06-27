<?php

namespace Modules\Facility\Services;

use App\Support\TypedValue;
use Modules\Core\Services\RevenueJournalHelper;
use Modules\Facility\Models\FacilityRental;
use Modules\Finance\Models\JournalEntry;
use RuntimeException;

class FacilityRentalJournalService
{
    public function __construct(
        private readonly RevenueJournalHelper $journalHelper,
    ) {}

    public function postForRental(FacilityRental $rental): JournalEntry
    {
        if ($rental->journal_entry_id) {
            return JournalEntry::query()->findOrFail($rental->journal_entry_id);
        }

        $amount = (float) $rental->total_amount;
        $tenantId = TypedValue::int($rental->tenant_id);
        $rentalId = TypedValue::string($rental->getKey());

        if ($amount <= 0) {
            throw new RuntimeException('Rental amount must be positive.');
        }

        $cash = $this->journalHelper->findCoa($tenantId, 'asset', ['kas', 'bank']);
        $revenue = $this->journalHelper->findCoa($tenantId, 'revenue', ['sewa', 'rental', 'pendapatan jasa']);

        if (! $cash || ! $revenue) {
            throw new RuntimeException('COA for facility rental journal not configured.');
        }

        $entry = $this->journalHelper->postIfMissing(
            tenantId: $tenantId,
            entryNumber: 'RENT-'.$rentalId,
            description: 'Facility rental #'.$rentalId,
            lines: [
                ['chart_of_account_id' => $cash->id, 'debit' => $amount, 'credit' => 0],
                ['chart_of_account_id' => $revenue->id, 'debit' => 0, 'credit' => $amount],
            ],
            date: $rental->starts_at,
        );

        $rental->forceFill(['journal_entry_id' => $entry->id])->save();

        return $entry;
    }
}
