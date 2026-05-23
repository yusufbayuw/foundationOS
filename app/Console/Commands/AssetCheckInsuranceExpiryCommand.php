<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Asset\Models\AssetInsurance;

class AssetCheckInsuranceExpiryCommand extends Command
{
    protected $signature = 'asset:check-insurance-expiry {--days=30}';

    protected $description = 'Flag asset insurance policies expiring within the notice window';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $count = AssetInsurance::query()
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<=', now()->addDays($days))
            ->whereDate('expires_at', '>=', now())
            ->update(['status' => 'expiring']);

        $this->info("Marked {$count} insurance record(s) as expiring.");

        return self::SUCCESS;
    }
}
