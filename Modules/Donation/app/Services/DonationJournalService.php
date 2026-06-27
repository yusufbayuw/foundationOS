<?php

namespace Modules\Donation\Services;

use App\Support\TypedValue;
use Illuminate\Support\Str;
use Modules\Core\Services\RevenueJournalHelper;
use Modules\Donation\Models\Donation;
use Modules\Finance\Models\JournalEntry;
use RuntimeException;

class DonationJournalService
{
    public function __construct(
        private readonly RevenueJournalHelper $journalHelper,
    ) {}

    public function postForPaidDonation(Donation $donation): JournalEntry
    {
        if ($donation->journal_entry_id) {
            return JournalEntry::query()->findOrFail($donation->journal_entry_id);
        }

        $tenantId = TypedValue::int($donation->tenant_id);
        $cash = $this->journalHelper->findCoa($tenantId, 'asset', ['kas', 'bank', 'cash']);
        $revenue = $this->journalHelper->findCoa($tenantId, 'revenue', ['donasi', 'donation', 'dana sosial']);

        if (! $cash || ! $revenue) {
            throw new RuntimeException('Chart of accounts for donation journal not configured.');
        }

        $amount = (float) $donation->amount;
        $entry = $this->journalHelper->postIfMissing(
            tenantId: $tenantId,
            entryNumber: 'DON-'.$donation->donation_number,
            description: 'Donation receipt '.$donation->donation_number,
            lines: [
                ['chart_of_account_id' => $cash->id, 'debit' => $amount, 'credit' => 0],
                ['chart_of_account_id' => $revenue->id, 'debit' => 0, 'credit' => $amount],
            ],
            date: $donation->paid_at ?? now(),
        );

        $donation->forceFill([
            'journal_entry_id' => $entry->id,
            'certificate_token' => $donation->certificate_token ?? Str::uuid()->toString(),
        ])->save();

        $donation->campaign?->increment('raised_amount', $amount);

        return $entry;
    }
}
