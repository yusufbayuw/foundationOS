<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\ItOps\Models\IpAddressRecord;
use Modules\ItOps\Models\SoftwareLicense;
use Modules\ItOps\Models\UserAccount;

class ItopsCheckExpiryCommand extends Command
{
    protected $signature = 'itops:check-expiry {--days=30}';

    protected $description = 'Mark IT licenses, accounts, and IP records that are nearing expiry';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $threshold = now()->addDays($days)->toDateString();

        $licenses = SoftwareLicense::query()
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<=', $threshold)
            ->update(['status' => 'expiring']);

        $accounts = UserAccount::query()
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<=', $threshold)
            ->update(['status' => 'expiring']);

        $ips = IpAddressRecord::query()
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<=', $threshold)
            ->update(['status' => 'expiring']);

        $this->info("Expiring: licenses={$licenses}, accounts={$accounts}, ips={$ips}");

        return self::SUCCESS;
    }
}
