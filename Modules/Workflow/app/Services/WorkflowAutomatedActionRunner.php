<?php

namespace Modules\Workflow\Services;

use Modules\Core\Models\User;
use Modules\Monitoring\Models\AuditLog;
use Modules\Workflow\Enums\WorkflowAutomationActionType;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Notifications\InternalWorkflowNotification;
use Throwable;

class WorkflowAutomatedActionRunner
{
    public function run(WorkflowInstance $instance, string $triggerEvent, array $context = []): void
    {
        $actions = collect(data_get($instance->workflow_snapshot, 'automated_actions', []))
            ->filter(fn (array $action): bool => ($action['is_active'] ?? true) && ($action['trigger_event'] ?? null) === $triggerEvent)
            ->sortBy('sort_order')
            ->values();

        foreach ($actions as $action) {
            $this->runAction($instance, $action, $context);
        }
    }

    protected function runAction(WorkflowInstance $instance, array $action, array $context): void
    {
        $type = $action['action_type'] ?? null;
        $config = (array) ($action['config'] ?? []);

        if (! $this->isAllowedActionType($type)) {
            $this->writeSkippedActionAudit($instance, $type, $config, $context, 'unsupported_action_type');

            return;
        }

        $this->runMappedAction($instance, $type, $config, $context);
    }

    protected function isAllowedActionType(mixed $type): bool
    {
        if (! is_string($type) || $type === '') {
            return false;
        }

        return in_array($type, array_column(WorkflowAutomationActionType::cases(), 'value'), true);
    }

    protected function runMappedAction(WorkflowInstance $instance, ?string $type, array $config, array $context): void
    {
        try {
            match ($type) {
                WorkflowAutomationActionType::InternalNotification->value => $this->sendInternalNotification($instance, $config),
                WorkflowAutomationActionType::AuditNote->value => $this->writeAuditNote($instance, $config, $context),
                WorkflowAutomationActionType::DispatchJob->value => $this->dispatchJob($instance, $config, $context),
                WorkflowAutomationActionType::SetComputedData->value => $this->setComputedData($instance, $config),
                default => null,
            };
        } catch (Throwable $exception) {
            $this->writeFailureAudit($instance, $type, $config, $exception, $context);

            report($exception);

            if (config('workflow.automation.rethrow_on_failure')) {
                throw $exception;
            }
        }
    }

    protected function sendInternalNotification(WorkflowInstance $instance, array $config): void
    {
        $recipientIds = collect($config['user_ids'] ?? [])
            ->map(fn ($value) => (int) $value)
            ->filter()
            ->all();

        if ($recipientIds === []) {
            $recipientIds = $instance->assignments()
                ->where('status', 'pending')
                ->pluck('assigned_to_id')
                ->all();
        }

        User::query()
            ->whereIn('id', $recipientIds)
            ->whereHas('userTenantRoles', fn ($query) => $query->where('tenant_id', $instance->tenant_id))
            ->get()
            ->each(fn (User $user) => $user->notify(new InternalWorkflowNotification(
                $instance,
                (string) ($config['title'] ?? 'Workflow update'),
                (string) ($config['body'] ?? 'A workflow requires your attention.'),
            )));
    }

    protected function writeAuditNote(WorkflowInstance $instance, array $config, array $context): void
    {
        AuditLog::query()->create([
            'tenant_id' => $instance->tenant_id,
            'organization_id' => $instance->organization_id,
            'user_id' => data_get($context, 'actor_id'),
            'auditable_type' => $instance->subject_type ?: $instance::class,
            'auditable_id' => $instance->subject_id ?: $instance->getKey(),
            'action' => (string) ($config['action'] ?? 'workflow_automation_note'),
            'description' => (string) ($config['description'] ?? 'Workflow automated action executed.'),
            'old_values' => null,
            'new_values' => [
                'workflow_instance_id' => $instance->getKey(),
                'trigger_event' => $context['trigger_event'] ?? null,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_id' => request()?->headers->get('X-Request-Id'),
            'status' => 'success',
            'error_message' => null,
        ]);
    }

    protected function dispatchJob(WorkflowInstance $instance, array $config, array $context): void
    {
        $jobClass = $config['job_class'] ?? null;

        if (! is_string($jobClass) || ! class_exists($jobClass)) {
            $this->writeSkippedActionAudit($instance, WorkflowAutomationActionType::DispatchJob->value, $config, $context, 'missing_job_class');

            return;
        }

        $allowedJobs = config('workflow.allowed_automation_jobs', []);

        if (! in_array($jobClass, $allowedJobs, true)) {
            $this->writeSkippedActionAudit($instance, WorkflowAutomationActionType::DispatchJob->value, $config, $context, 'job_not_allowlisted');

            return;
        }

        $jobClass::dispatch($instance->getKey(), $config['payload'] ?? []);
    }

    protected function setComputedData(WorkflowInstance $instance, array $config): void
    {
        $data = (array) ($config['data'] ?? []);

        if ($data === []) {
            return;
        }

        $instance->forceFill([
            'computed_data' => array_replace_recursive($instance->computed_data ?? [], $data),
        ])->save();
    }

    protected function writeSkippedActionAudit(
        WorkflowInstance $instance,
        ?string $type,
        array $config,
        array $context,
        string $reason,
    ): void {
        AuditLog::query()->create([
            'tenant_id' => $instance->tenant_id,
            'organization_id' => $instance->organization_id,
            'user_id' => data_get($context, 'actor_id'),
            'auditable_type' => $instance->subject_type ?: $instance::class,
            'auditable_id' => $instance->subject_id ?: $instance->getKey(),
            'action' => 'workflow_automation_skipped',
            'description' => 'Workflow automated action was skipped by security policy.',
            'old_values' => null,
            'new_values' => [
                'workflow_instance_id' => $instance->getKey(),
                'trigger_event' => $context['trigger_event'] ?? null,
                'action_type' => $type,
                'action_name' => $config['name'] ?? null,
                'job_class' => $config['job_class'] ?? null,
                'reason' => $reason,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_id' => request()?->headers->get('X-Request-Id'),
            'status' => 'failed',
            'error_message' => $reason,
        ]);
    }

    protected function writeFailureAudit(WorkflowInstance $instance, ?string $type, array $config, Throwable $exception, array $context): void
    {
        AuditLog::query()->create([
            'tenant_id' => $instance->tenant_id,
            'organization_id' => $instance->organization_id,
            'user_id' => data_get($context, 'actor_id'),
            'auditable_type' => $instance->subject_type ?: $instance::class,
            'auditable_id' => $instance->subject_id ?: $instance->getKey(),
            'action' => 'workflow_automation_failed',
            'description' => 'Workflow automated action failed.',
            'old_values' => null,
            'new_values' => [
                'workflow_instance_id' => $instance->getKey(),
                'trigger_event' => $context['trigger_event'] ?? null,
                'action_type' => $type,
                'action_name' => $config['name'] ?? null,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_id' => request()?->headers->get('X-Request-Id'),
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}
