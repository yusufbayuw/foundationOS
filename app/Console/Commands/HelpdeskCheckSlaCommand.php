<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Helpdesk\Models\Ticket;

class HelpdeskCheckSlaCommand extends Command
{
    protected $signature = 'helpdesk:check-sla';

    protected $description = 'Escalate helpdesk tickets that have breached SLA thresholds';

    public function handle(): int
    {
        Ticket::query()
            ->where('status', 'open')
            ->whereNotNull('meta')
            ->each(function (Ticket $ticket): void {
                $slaDue = data_get($ticket->meta, 'sla_due_at');
                if ($slaDue && now()->greaterThan($slaDue)) {
                    $ticket->update([
                        'status' => 'escalated',
                        'meta' => array_merge($ticket->meta ?? [], ['escalated_at' => now()->toIso8601String()]),
                    ]);
                }
            });

        $this->info('SLA check completed.');

        return self::SUCCESS;
    }
}
