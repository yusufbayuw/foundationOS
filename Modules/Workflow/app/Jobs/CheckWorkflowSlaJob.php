<?php

namespace Modules\Workflow\Jobs;

use App\Concerns\InteractsWithTenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Workflow\Contracts\WorkflowSlaService;
use Modules\Workflow\Models\WorkflowInstance;

class CheckWorkflowSlaJob implements ShouldQueue
{
    use InteractsWithTenant, Queueable;

    public function __construct(public int $instanceId)
    {
        $this->onQueue((string) config('workflow.sla_queue', 'workflow-sla'));
        $this->captureCurrentTenant();
    }

    public function handle(WorkflowSlaService $slaService): void
    {
        /** @var WorkflowInstance|null $instance */
        $instance = WorkflowInstance::query()->find($this->instanceId);

        if (! $instance || $instance->due_at === null || $instance->completed_at !== null || $instance->cancelled_at !== null || $instance->rejected_at !== null) {
            return;
        }

        if ($instance->due_at->isPast()) {
            $slaService->markBreached($instance);
        }
    }
}
