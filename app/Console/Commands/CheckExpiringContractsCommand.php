<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Legal\Events\ContractExpiringSoon;
use Modules\Legal\Models\Contract;

class CheckExpiringContractsCommand extends Command
{
    protected $signature = 'legal:check-expiring-contracts {--days=30}';

    protected $description = 'Notify contract owners about contracts expiring within the notice period';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        Contract::query()
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<=', now()->addDays($days))
            ->whereDate('expires_at', '>=', now())
            ->each(function (Contract $contract): void {
                event(new ContractExpiringSoon($contract));
            });

        $this->info('Expiring contract scan completed.');

        return self::SUCCESS;
    }
}
