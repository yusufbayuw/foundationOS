<?php

namespace Modules\Workflow\Listeners;

use App\Concerns\InteractsWithTenant;
use App\Support\TypedValue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowCancelled;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Events\WorkflowSlaBreached;
use Modules\Workflow\Events\WorkflowStarted;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Services\WorkflowAutomatedActionRunner;
use Throwable;

class RunWorkflowAutomatedActions implements ShouldQueue
{
    use InteractsWithTenant, Queueable;

    public function __construct(private readonly WorkflowAutomatedActionRunner $runner) {}

    public function handle(WorkflowStarted|WorkflowAdvanced|WorkflowCancelled|WorkflowReturned|WorkflowSlaBreached $event): void
    {
        if ($event->instance->tenant_id) {
            $this->onTenant((int) $event->instance->tenant_id);
        }

        try {
            $this->runForEvent($event);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    protected function runForEvent(WorkflowStarted|WorkflowAdvanced|WorkflowCancelled|WorkflowReturned|WorkflowSlaBreached $event): void
    {
        $instance = TypedValue::model($event->instance->fresh());

        match ($event::class) {
            WorkflowStarted::class => $this->runner->run($instance, 'started', [
                'actor_id' => $event->actor->getKey(),
                'trigger_event' => 'started',
            ]),
            WorkflowAdvanced::class => $this->runAdvanced($instance, $event),
            WorkflowCancelled::class => $this->runner->run($instance, 'cancelled', [
                'actor_id' => $event->actor->getKey(),
                'trigger_event' => 'cancelled',
            ]),
            WorkflowReturned::class => $this->runner->run($instance, 'returned', [
                'actor_id' => $event->actor->getKey(),
                'trigger_event' => 'returned',
            ]),
            WorkflowSlaBreached::class => $this->runner->run($instance, 'sla_breached', [
                'trigger_event' => 'sla_breached',
            ]),
            default => null,
        };
    }

    protected function runAdvanced(WorkflowInstance $instance, WorkflowAdvanced $event): void
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
