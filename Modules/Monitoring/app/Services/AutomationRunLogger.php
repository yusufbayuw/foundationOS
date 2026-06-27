<?php

namespace Modules\Monitoring\Services;

use Illuminate\Database\Eloquent\Model;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\AutomationRun;
use Throwable;

/**
 * Wraps cross-module automation with lineage in automation_runs + audit_logs.
 */
class AutomationRunLogger
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function run(
        string $triggerEvent,
        Model $subject,
        callable $action,
        string $actionType,
        ?int $parentRunId = null,
        array $payload = [],
    ): mixed {
        $run = AutomationRun::query()->create([
            'tenant_id' => $subject->getAttribute('tenant_id'),
            'parent_id' => $parentRunId,
            'trigger_event' => $triggerEvent,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'action_type' => $actionType,
            'status' => AutomationRun::STATUS_PENDING,
            'payload' => $payload,
        ]);

        $run->markRunning();

        try {
            $result = $action();
            $run->markCompleted($this->normalizeResult($result));

            $this->writeAudit($run, $subject, 'automation_completed', [
                'trigger_event' => $triggerEvent,
                'action_type' => $actionType,
                'automation_run_id' => $run->getKey(),
            ]);

            return $result;
        } catch (Throwable $exception) {
            $run->markFailed($exception);

            $this->writeAudit($run, $subject, 'automation_failed', [
                'trigger_event' => $triggerEvent,
                'action_type' => $actionType,
                'automation_run_id' => $run->getKey(),
                'error' => $exception->getMessage(),
            ], 'failed');

            throw $exception;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function normalizeResult(mixed $result): ?array
    {
        if ($result === null) {
            return null;
        }

        if (is_array($result)) {
            return $result;
        }

        if ($result instanceof Model) {
            return [
                'model' => $result->getMorphClass(),
                'id' => $result->getKey(),
            ];
        }

        return ['value' => $result];
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    protected function writeAudit(
        AutomationRun $run,
        Model $subject,
        string $action,
        array $metadata,
        string $status = 'success',
    ): void {
        AuditLog::query()->create([
            'tenant_id' => $run->tenant_id,
            'user_id' => auth()->id(),
            'organization_id' => $subject->getAttribute('organization_id'),
            'auditable_type' => $subject->getMorphClass(),
            'auditable_id' => $subject->getKey(),
            'action' => $action,
            'category' => 'automation',
            'description' => sprintf('%s:%s', $run->trigger_event, $run->action_type),
            'new_values' => $metadata,
            'status' => $status,
        ]);
    }
}
