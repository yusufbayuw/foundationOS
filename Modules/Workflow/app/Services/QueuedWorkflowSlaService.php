<?php

namespace Modules\Workflow\Services;

use Carbon\CarbonInterface;
use Modules\Workflow\Contracts\WorkflowAuditLogger;
use Modules\Workflow\Contracts\WorkflowSlaService;
use Modules\Workflow\Enums\WorkflowLogType;
use Modules\Workflow\Events\WorkflowSlaBreached;
use Modules\Workflow\Jobs\CheckWorkflowSlaJob;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;

class QueuedWorkflowSlaService implements WorkflowSlaService
{
    public function __construct(private readonly WorkflowAuditLogger $auditLogger) {}

    public function computeDueAt(?WorkflowStep $step, CarbonInterface $from): ?CarbonInterface
    {
        if (! $step || ! $step->sla_hours) {
            return null;
        }

        return $from->copy()->addHours((int) $step->sla_hours);
    }

    public function scheduleCheck(WorkflowInstance $instance): void
    {
        if (! $instance->due_at || $instance->completed_at || $instance->cancelled_at || $instance->rejected_at) {
            return;
        }

        CheckWorkflowSlaJob::dispatch($instance->getKey())
            ->delay($instance->due_at);

        $this->auditLogger->log($instance, WorkflowLogType::SlaScheduled->value, [
            'notes' => 'SLA check scheduled.',
        ]);
    }

    public function markBreached(WorkflowInstance $instance): void
    {
        $this->auditLogger->log($instance, WorkflowLogType::SlaBreached->value, [
            'notes' => 'Workflow SLA breached.',
        ]);

        WorkflowSlaBreached::dispatch($instance);
    }
}
