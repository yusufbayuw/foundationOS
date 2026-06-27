<?php

namespace Modules\Workflow\Services;

use App\Support\TypedValue;
use Modules\Core\Models\User;
use Modules\Monitoring\Models\AuditLog;
use Modules\Workflow\Enums\WorkflowAutomationActionType;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Notifications\InternalWorkflowNotification;
use Throwable;

class WorkflowAutomatedActionRunner
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function run(WorkflowInstance $instance, string $triggerEvent, array $context = []): void
    {
        /** @var list<array<string, mixed>> $configuredActions */
        $configuredActions = data_get($instance->workflow_snapshot, 'automated_actions', []);

        $actions = collect($configuredActions)
            ->filter(fn (array $action): bool => ($action['is_active'] ?? true) && ($action['trigger_event'] ?? null) === $triggerEvent)
            ->sortBy('sort_order')
            ->values();

        foreach ($actions as $action) {
            $this->runAction($instance, $action, $context);
        }
    }

    /**
     * @param  array<string, mixed>  $action
     * @param  array<string, mixed>  $context
     */
    protected function runAction(WorkflowInstance $instance, array $action, array $context): void
    {
        $type = $action['action_type'] ?? null;
        $config = $this->normalizeConfig($action['config'] ?? []);

        match ($type) {
            WorkflowAutomationActionType::InternalNotification->value,
            WorkflowAutomationActionType::AuditNote->value,
            WorkflowAutomationActionType::DispatchJob->value,
            WorkflowAutomationActionType::SetComputedData->value => $this->runMappedAction($instance, $type, $config, $context),
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $context
     */
    protected function runMappedAction(WorkflowInstance $instance, ?string $type, array $config, array $context): void
    {
        try {
            match ($type) {
                WorkflowAutomationActionType::InternalNotification->value => $this->sendInternalNotification($instance, $config),
                WorkflowAutomationActionType::AuditNote->value => $this->writeAuditNote($instance, $config, $context),
                WorkflowAutomationActionType::DispatchJob->value => $this->dispatchJob($instance, $config),
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

    /**
     * @param  array<string, mixed>  $config
     */
    protected function sendInternalNotification(WorkflowInstance $instance, array $config): void
    {
        /** @var mixed $configuredUserIds */
        $configuredUserIds = $config['user_ids'] ?? [];
        /** @var list<int> $recipientIds */
        $recipientIds = is_array($configuredUserIds)
            ? collect($configuredUserIds)
                ->map(fn (mixed $value): int => TypedValue::int($value))
                ->filter(fn (int $id): bool => $id > 0)
                ->values()
                ->all()
            : [];

        if ($recipientIds === []) {
            $recipientIds = $instance->assignments()
                ->where('status', 'pending')
                ->pluck('assigned_to_id')
                ->all();
        }

        User::query()
            ->whereIn('id', $recipientIds)
            ->get()
            ->each(fn (User $user) => $user->notify(new InternalWorkflowNotification(
                $instance,
                TypedValue::string($config['title'] ?? 'Workflow update'),
                TypedValue::string($config['body'] ?? 'A workflow requires your attention.'),
            )));
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $context
     */
    protected function writeAuditNote(WorkflowInstance $instance, array $config, array $context): void
    {
        AuditLog::query()->create([
            'tenant_id' => $instance->tenant_id,
            'organization_id' => $instance->organization_id,
            'user_id' => data_get($context, 'actor_id'),
            'auditable_type' => $instance->subject_type ?: $instance::class,
            'auditable_id' => $instance->subject_id ?: $instance->getKey(),
            'action' => TypedValue::string($config['action'] ?? 'workflow_automation_note'),
            'description' => TypedValue::string($config['description'] ?? 'Workflow automated action executed.'),
            'old_values' => null,
            'new_values' => [
                'workflow_instance_id' => $instance->getKey(),
                'trigger_event' => $context['trigger_event'] ?? null,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_id' => request()->headers->get('X-Request-Id'),
            'status' => 'success',
            'error_message' => null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function dispatchJob(WorkflowInstance $instance, array $config): void
    {
        $jobClass = $config['job_class'] ?? null;

        if (! is_string($jobClass) || ! class_exists($jobClass)) {
            return;
        }

        $allowedJobs = config('workflow.allowed_automation_jobs', []);
        if (! is_array($allowedJobs)) {
            return;
        }

        if (! in_array($jobClass, $allowedJobs, true)) {
            return;
        }

        $jobClass::dispatch($instance->getKey(), $config['payload'] ?? []);
    }

    /**
     * @param  array<string, mixed>  $config
     */
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

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $context
     */
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
            'request_id' => request()->headers->get('X-Request-Id'),
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeConfig(mixed $config): array
    {
        if (! is_array($config)) {
            return [];
        }

        $normalized = [];

        foreach ($config as $key => $value) {
            $normalized[(string) $key] = $value;
        }

        return $normalized;
    }
}
