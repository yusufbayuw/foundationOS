<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Donation\Models\CampaignUpdate;

class DonationSendCampaignUpdateCommand extends Command
{
    protected $signature = 'donation:send-campaign-update';

    protected $description = 'Send published campaign updates to campaign donors';

    public function handle(): int
    {
        $updates = CampaignUpdate::withoutTenantScope()
            ->where('status', 'published')
            ->whereNull('published_at')
            ->with('campaign.donations.donor')
            ->get();

        foreach ($updates as $update) {
            $donorCount = $update->campaign?->donations()
                ->where('payment_status', 'paid')
                ->distinct('donor_id')
                ->count('donor_id') ?? 0;

            $update->forceFill(['published_at' => now()])->saveQuietly();
            $this->line("Campaign update #{$update->id} queued for {$donorCount} donors.");
        }

        return self::SUCCESS;
    }
}
