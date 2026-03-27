<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Services\WorkflowAutomatedActionRunner;

class WorkflowRetryAutomationCommand extends Command
{
    protected $signature = 'fos:workflow:retry-automation
        {instance : Workflow instance ID}
        {trigger : started|advanced|completed|rejected|cancelled|returned|sla_breached}';

    protected $description = 'Manually rerun automated actions for a workflow instance trigger';

    public function handle(WorkflowAutomatedActionRunner $runner): int
    {
        $instance = WorkflowInstance::query()->findOrFail((int) $this->argument('instance'));
        $trigger = trim((string) $this->argument('trigger'));

        $runner->run($instance, $trigger, [
            'trigger_event' => $trigger,
            'actor_id' => optional(auth()->user())->getKey(),
            'manual_retry' => true,
        ]);

        $this->info("Automated actions rerun for workflow instance {$instance->id} with trigger '{$trigger}'.");

        return self::SUCCESS;
    }
}
