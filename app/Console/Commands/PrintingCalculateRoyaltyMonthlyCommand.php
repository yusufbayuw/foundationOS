<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Printing\Models\Royalty;

class PrintingCalculateRoyaltyMonthlyCommand extends Command
{
    protected $signature = 'printing:calculate-royalty-monthly';

    protected $description = 'Calculate monthly author royalties';

    public function handle(): int
    {
        $period = now()->subMonth()->format('Y-m');
        $count = Royalty::withoutTenantScope()
            ->where('status', 'pending')
            ->where('description', 'like', '%'.$period.'%')
            ->update(['status' => 'calculated']);

        $this->info("Calculated {$count} royalty records for {$period}.");

        return self::SUCCESS;
    }
}
