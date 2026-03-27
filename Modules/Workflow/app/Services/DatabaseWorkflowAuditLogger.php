<?php

namespace Modules\Workflow\Services;

use Modules\Workflow\Contracts\WorkflowAuditLogger;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowInstanceLog;

class DatabaseWorkflowAuditLogger implements WorkflowAuditLogger
{
    public function log(WorkflowInstance $instance, string $logType, array $context = []): WorkflowInstanceLog
    {
        return WorkflowInstanceLog::query()->create([
            'workflow_instance_id' => $instance->getKey(),
            'step_id' => $context['step_id'] ?? $instance->current_step_id,
            'transition_id' => $context['transition_id'] ?? null,
            'actor_id' => $context['actor_id'] ?? null,
            'log_type' => $logType,
            'action_taken' => $context['action_taken'] ?? null,
            'status_before' => $context['status_before'] ?? null,
            'status_after' => $context['status_after'] ?? ($instance->status?->value ?? $instance->status),
            'payload_before' => $context['payload_before'] ?? null,
            'payload_after' => $context['payload_after'] ?? [
                'context_data' => $instance->context_data,
                'form_data' => $instance->form_data,
                'computed_data' => $instance->computed_data,
                'current_assignees' => $instance->current_assignees,
            ],
            'form_data_snapshot' => $context['form_data_snapshot'] ?? null,
            'notes' => $context['notes'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_id' => request()?->headers->get('X-Request-Id'),
            'logged_at' => now(),
        ]);
    }
}
