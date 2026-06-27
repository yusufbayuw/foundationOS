<?php

namespace Modules\Workflow\Listeners;

use App\Support\TypedValue;
use Modules\Monitoring\Models\AuditLog;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowCancelled;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Events\WorkflowSlaBreached;
use Modules\Workflow\Events\WorkflowStarted;

class RecordWorkflowMonitoringAudit
{
    public function handle(WorkflowStarted|WorkflowAdvanced|WorkflowReturned|WorkflowCancelled|WorkflowSlaBreached $event): void
    {
        $instance = $event->instance;
        $actor = property_exists($event, 'actor') ? $event->actor : null;

        AuditLog::query()->create([
            'tenant_id' => $instance->tenant_id,
            'organization_id' => $instance->organization_id,
            'user_id' => TypedValue::nullableInt(data_get($actor, 'id')),
            'auditable_type' => $instance->getMorphClass(),
            'auditable_id' => $instance->getKey(),
            'action' => match ($event::class) {
                WorkflowStarted::class => 'workflow_started',
                WorkflowAdvanced::class => 'workflow_advanced',
                WorkflowReturned::class => 'workflow_returned',
                WorkflowCancelled::class => 'workflow_cancelled',
                WorkflowSlaBreached::class => 'workflow_sla_breached',
                default => 'workflow_event',
            },
            'description' => 'Workflow event recorded.',
            'old_values' => null,
            'new_values' => [
                'status' => $instance->status->value ?? $instance->status,
                'current_step_id' => $instance->current_step_id,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_id' => request()->headers->get('X-Request-Id'),
            'status' => 'success',
            'error_message' => null,
        ]);
    }
}
