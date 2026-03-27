<?php

namespace Modules\Workflow\Listeners;

use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowCancelled;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Events\WorkflowSlaBreached;
use Modules\Workflow\Events\WorkflowStarted;
use Modules\Workflow\Services\WorkflowAutomatedActionRunner;

class RunWorkflowAutomatedActions
{
    public function __construct(private readonly WorkflowAutomatedActionRunner $runner) {}

    public function handle(object $event): void
    {
        $instance = $event->instance->fresh();

        match (true) {
            $event instanceof WorkflowStarted => $this->runner->run($instance, 'started', [
                'actor_id' => $event->actor->getKey(),
                'trigger_event' => 'started',
            ]),
            $event instanceof WorkflowAdvanced => $this->runAdvanced($instance, $event),
            $event instanceof WorkflowCancelled => $this->runner->run($instance, 'cancelled', [
                'actor_id' => $event->actor->getKey(),
                'trigger_event' => 'cancelled',
            ]),
            $event instanceof WorkflowReturned => $this->runner->run($instance, 'returned', [
                'actor_id' => $event->actor->getKey(),
                'trigger_event' => 'returned',
            ]),
            $event instanceof WorkflowSlaBreached => $this->runner->run($instance, 'sla_breached', [
                'trigger_event' => 'sla_breached',
            ]),
            default => null,
        };
    }

    protected function runAdvanced($instance, WorkflowAdvanced $event): void
    {
        $baseContext = [
            'actor_id' => $event->actor->getKey(),
            'transition_id' => $event->transition->getKey(),
        ];

        $this->runner->run($instance, 'advanced', $baseContext + ['trigger_event' => 'advanced']);

        if ($instance->status === WorkflowInstanceStatus::Completed) {
            $this->runner->run($instance, 'completed', $baseContext + ['trigger_event' => 'completed']);
        }

        if ($instance->status === WorkflowInstanceStatus::Rejected) {
            $this->runner->run($instance, 'rejected', $baseContext + ['trigger_event' => 'rejected']);
        }
    }
}
