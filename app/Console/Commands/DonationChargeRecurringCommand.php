<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Donation\Models\Donation;
use Modules\Donation\Models\RecurringDonation;
use Modules\Donation\Services\DonationJournalService;

class DonationChargeRecurringCommand extends Command
{
    protected $signature = 'donation:charge-recurring';

    protected $description = 'Charge due recurring donations (idempotent)';

    public function __construct(
        private readonly DonationJournalService $journalService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $due = RecurringDonation::withoutTenantScope()
            ->where('status', 'active')
            ->whereDate('next_charge_date', '<=', today())
            ->get();

        foreach ($due as $recurring) {
            $key = 'recurring-'.$recurring->id.'-'.$recurring->next_charge_date->format('Y-m-d');

            if (Donation::withoutTenantScope()->where('payment_reference', $key)->exists()) {
                continue;
            }

            $donation = Donation::withoutTenantScope()->create([
                'tenant_id' => $recurring->tenant_id,
                'campaign_id' => $recurring->campaign_id,
                'donor_id' => $recurring->donor_id,
                'donation_number' => 'DON-R-'.Str::upper(Str::random(8)),
                'amount' => $recurring->amount,
                'payment_status' => 'paid',
                'payment_reference' => $key,
                'paid_at' => now(),
            ]);

            $this->journalService->postForPaidDonation($donation);

            $recurring->forceFill([
                'next_charge_date' => $recurring->next_charge_date->addMonth(),
            ])->save();
        }

        $this->info('Processed '.$due->count().' recurring charges.');

        return self::SUCCESS;
    }
}
