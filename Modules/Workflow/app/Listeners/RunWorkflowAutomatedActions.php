<?php

namespace Modules\Workflow\Listeners;

use App\Concerns\InteractsWithTenant;
use App\Support\CurrentTenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowCancelled;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Events\WorkflowSlaBreached;
use Modules\Workflow\Events\WorkflowStarted;
use Modules\Workflow\Services\WorkflowAutomatedActionRunner;
use Throwable;

class RunWorkflowAutomatedActions implements ShouldQueue
{
    use InteractsWithTenant, Queueable;

    public function __construct(private readonly WorkflowAutomatedActionRunner $runner) {}

    public function handle(object $event): void
    {
        $tenantId = property_exists($event, 'instance') ? $event->instance?->tenant_id : $this->tenantId;

        $runner = function () use ($event): void {
            try {
                $this->runForEvent($event);
            } catch (Throwable $exception) {
                report($exception);
            }
        };

        if ($tenantId) {
            app(CurrentTenant::class)->forTenant((int) $tenantId, $runner);

            return;
        }

        $runner();
    }

    protected function runForEvent(object $event): void
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
