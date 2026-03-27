<?php

namespace Modules\Workflow\Listeners;

use Modules\Monitoring\Models\AuditLog;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowCancelled;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Events\WorkflowSlaBreached;
use Modules\Workflow\Events\WorkflowStarted;

class RecordWorkflowMonitoringAudit
{
    public function handle(object $event): void
    {
        $instance = $event->instance;
        $actor = property_exists($event, 'actor') ? $event->actor : null;

        AuditLog::query()->create([
            'tenant_id' => $instance->tenant_id,
            'organization_id' => $instance->organization_id,
            'user_id' => $actor?->getKey(),
            'auditable_type' => $instance::class,
            'auditable_id' => $instance->getKey(),
            'action' => match (true) {
                $event instanceof WorkflowStarted => 'workflow_started',
                $event instanceof WorkflowAdvanced => 'workflow_advanced',
                $event instanceof WorkflowReturned => 'workflow_returned',
                $event instanceof WorkflowCancelled => 'workflow_cancelled',
                $event instanceof WorkflowSlaBreached => 'workflow_sla_breached',
                default => 'workflow_event',
            },
            'description' => 'Workflow event recorded.',
            'old_values' => null,
            'new_values' => [
                'status' => $instance->status?->value ?? $instance->status,
                'current_step_id' => $instance->current_step_id,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_id' => request()?->headers->get('X-Request-Id'),
            'status' => 'success',
            'error_message' => null,
        ]);
    }
}
