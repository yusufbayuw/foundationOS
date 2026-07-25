<?php

namespace Modules\Workflow\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;

class WorkflowCanvasService
{
    public function __construct(
        private readonly WorkflowDefinitionLifecycleService $lifecycle,
        private readonly WorkflowDefinitionPorter $porter,
    ) {}

    /**
     * @return array{workflow: array<string, mixed>, steps: array<int, array<string, mixed>>, transitions: array<int, array<string, mixed>>}|null
     */
    public function canvasForWorkflow(int $workflowId): ?array
    {
        $workflow = Workflow::query()
            ->with(['steps' => fn ($query) => $query->orderBy('sort_order'), 'transitions'])
            ->find($workflowId);

        if (! $workflow) {
            return null;
        }

        $steps = $workflow->steps->map(fn (WorkflowStep $step): array => [
            'uuid' => $step->uuid,
            'code' => $step->code,
            'name' => $step->name,
            'description' => $step->description ?? '',
            'step_type' => $step->step_type?->value ?? 'task',
            'gateway_type' => $step->gateway_type?->value ?? 'none',
            'quorum_strategy' => $step->quorum_strategy?->value,
            'quorum_value' => $step->quorum_value,
            'assignee_type' => $step->assignee_type?->value ?? 'user',
            'assignee_value' => $step->assignee_value ?? '',
            'assignee_config' => $step->assignee_config ?? [],
            'form_schema' => $step->form_schema ?? [],
            'sla_hours' => $step->sla_hours,
            'is_initial' => (bool) $step->is_initial,
            'is_terminal' => (bool) $step->is_terminal,
            'sort_order' => $step->sort_order ?? 0,
            'canvas_position' => $step->canvas_position ?? null,
        ])->values()->all();

        return [
            'workflow' => [
                'id' => $workflow->id,
                'code' => $workflow->code,
                'name' => $workflow->name,
                'description' => $workflow->description ?? '',
                'status' => $workflow->status?->value ?? 'draft',
            ],
            'steps' => $steps,
            'transitions' => $workflow->transitions->map(function (WorkflowTransition $transition) use ($workflow): array {
                $fromUuid = $workflow->steps->firstWhere('id', $transition->from_step_id)?->uuid;
                $toUuid = $transition->to_step_id ? $workflow->steps->firstWhere('id', $transition->to_step_id)?->uuid : null;

                return [
                    'from_uuid' => $fromUuid,
                    'to_uuid' => $toUuid,
                    'action_name' => $transition->action_name,
                    'priority' => $transition->priority ?? 0,
                    'is_default' => (bool) $transition->is_default,
                    'condition_rules' => $transition->condition_rules ?? [],
                ];
            })->values()->all(),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $steps
     * @param  array<int, array<string, mixed>>  $transitions
     */
    public function persistCanvas(
        int $tenantId,
        ?int $workflowId,
        string $workflowCode,
        string $workflowName,
        string $workflowDescription,
        array $steps,
        array $transitions,
        ?int $actorId = null,
    ): Workflow {
        return DB::transaction(function () use ($tenantId, $workflowId, $workflowCode, $workflowName, $workflowDescription, $steps, $transitions, $actorId): Workflow {
            if ($workflowId) {
                $workflow = Workflow::query()->findOrFail($workflowId);

                if ($workflow->status === WorkflowDefinitionStatus::Active) {
                    $workflow = $this->lifecycle->duplicateAsNewVersion($workflow, $actorId);
                }

                $workflow->update([
                    'name' => $workflowName ?: $workflow->name,
                    'description' => $workflowDescription ?: $workflow->description,
                ]);

                $workflow->transitions()->forceDelete();
                $workflow->steps()->forceDelete();
            } else {
                $code = $workflowCode ?: 'wf_'.Str::random(6);
                $version = (int) Workflow::query()->where('tenant_id', $tenantId)->where('code', $code)->max('version') + 1;

                $workflow = Workflow::query()->create([
                    'tenant_id' => $tenantId,
                    'code' => $code,
                    'name' => $workflowName ?: 'New Workflow',
                    'description' => $workflowDescription ?: null,
                    'version' => $version,
                    'status' => WorkflowDefinitionStatus::Draft,
                    'is_active' => false,
                ]);
            }

            $oldToNewUuid = [];
            $stepsWithNewUuids = [];
            foreach ($steps as $stepData) {
                $newUuid = (string) Str::uuid();
                $oldToNewUuid[$stepData['uuid']] = $newUuid;
                $stepsWithNewUuids[] = array_merge($stepData, ['uuid' => $newUuid]);
            }

            $uuidToId = [];
            foreach ($stepsWithNewUuids as $order => $stepData) {
                $step = WorkflowStep::query()->create([
                    'workflow_id' => $workflow->id,
                    'uuid' => $stepData['uuid'],
                    'code' => $stepData['code'],
                    'name' => $stepData['name'],
                    'description' => ($stepData['description'] ?? null) ?: null,
                    'step_type' => $stepData['step_type'],
                    'gateway_type' => $stepData['gateway_type'] ?? 'none',
                    'quorum_strategy' => ($stepData['quorum_strategy'] ?? null) ?: null,
                    'quorum_value' => ($stepData['quorum_value'] ?? null) ?: null,
                    'assignee_type' => $stepData['assignee_type'],
                    'assignee_value' => ($stepData['assignee_value'] ?? null) ?: null,
                    'assignee_config' => ($stepData['assignee_config'] ?? null) ?: null,
                    'form_schema' => ($stepData['form_schema'] ?? null) ?: null,
                    'sla_hours' => ($stepData['sla_hours'] ?? null) ?: null,
                    'is_initial' => (bool) $stepData['is_initial'],
                    'is_terminal' => (bool) $stepData['is_terminal'],
                    'sort_order' => $order,
                    'canvas_position' => ($stepData['canvas_position'] ?? null) ?: null,
                ]);

                $uuidToId[$stepData['uuid']] = $step->id;
            }

            foreach ($transitions as $transitionData) {
                $fromUuid = $oldToNewUuid[$transitionData['from_uuid']] ?? $transitionData['from_uuid'];
                $toUuid = isset($transitionData['to_uuid']) ? ($oldToNewUuid[$transitionData['to_uuid']] ?? $transitionData['to_uuid']) : null;

                WorkflowTransition::query()->create([
                    'workflow_id' => $workflow->id,
                    'from_step_id' => $uuidToId[$fromUuid] ?? null,
                    'to_step_id' => $toUuid ? ($uuidToId[$toUuid] ?? null) : null,
                    'action_name' => $transitionData['action_name'],
                    'priority' => $transitionData['priority'] ?? 0,
                    'is_default' => (bool) $transitionData['is_default'],
                    'condition_rules' => ($transitionData['condition_rules'] ?? null) ?: null,
                ]);
            }

            return $workflow->fresh(['steps', 'transitions']);
        });
    }

    /** @return string[] */
    public function validateImportPayload(array $payload): array
    {
        return $this->porter->validatePayload($payload);
    }

    public function importPayload(array $payload, int $tenantId): Workflow
    {
        return $this->porter->import($payload, $tenantId);
    }

    public function exportPayload(Workflow $workflow): array
    {
        return $this->porter->export($workflow);
    }
}
