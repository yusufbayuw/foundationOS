<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\PhysicalSecurity\Models\SafetyChecklist;

class SafetyDailyRoundsCommand extends Command
{
    protected $signature = 'safety:daily-rounds';

    protected $description = 'Create daily safety checklist rounds for active tenants';

    public function handle(): int
    {
        $count = 0;

        SafetyChecklist::query()
            ->where('status', 'active')
            ->each(function (SafetyChecklist $checklist) use (&$count): void {
                $checklist->update([
                    'meta' => array_merge($checklist->meta ?? [], [
                        'last_round_at' => now()->toIso8601String(),
                    ]),
                ]);
                $count++;
            });

        $this->info("Updated {$count} safety checklist round(s).");

        return self::SUCCESS;
    }
}
