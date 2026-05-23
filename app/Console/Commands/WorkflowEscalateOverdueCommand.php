<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Workflow\Services\WorkflowEscalationService;

class WorkflowEscalateOverdueCommand extends Command
{
    protected $signature = 'workflow:escalate-overdue';

    protected $description = 'Escalate overdue workflow assignments.';

    public function handle(WorkflowEscalationService $service): int
    {
        $count = $service->escalateOverdue(now());

        $this->info("Escalated {$count} overdue workflow assignments.");

        return self::SUCCESS;
    }
}
