<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Asset\Models\AssetMaintenance;

class AssetCheckMaintenanceDueCommand extends Command
{
    protected $signature = 'asset:check-maintenance-due';

    protected $description = 'Flag asset maintenance records that are due or overdue';

    public function handle(): int
    {
        AssetMaintenance::query()
            ->where('status', 'scheduled')
            ->whereDate('meta->due_at', '<=', now())
            ->update(['status' => 'due']);

        $this->info('Asset maintenance due check completed.');

        return self::SUCCESS;
    }
}
