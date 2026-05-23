<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Training\Models\TrainingAffiliateCommission;

class TrainingSettleAffiliateCommand extends Command
{
    protected $signature = 'training:settle-affiliate';

    protected $description = 'Settle pending training affiliate commissions';

    public function handle(): int
    {
        $count = TrainingAffiliateCommission::withoutTenantScope()
            ->where('status', 'pending')
            ->update(['status' => 'settled']);

        $this->info("Settled {$count} affiliate commissions.");

        return self::SUCCESS;
    }
}
