<?php

namespace Modules\Workflow\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowAutomatedAction;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;

class WorkflowDefinitionPorter
{
    private const SCHEMA_VERSION = '1.0';

    /**
     * Export a workflow definition to a portable JSON-safe array.
     * Database IDs are replaced with step UUIDs for portability.
     */
    public function export(Workflow $workflow): array
    {
        $workflow = $workflow->fresh(['steps', 'transitions', 'automatedActions']);

        $steps = $workflow->steps()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (WorkflowStep $step) => [
                'uuid' => $step->uuid,
                'code' => $step->code,
                'name' => $step->name,
                'description' => $step->description,
                'step_type' => $step->step_type?->value,
                'gateway_type' => $step->gateway_type?->value ?? 'none',
                'quorum_strategy' => $step->quorum_strategy?->value,
                'quorum_value' => $step->quorum_value,
                'assignee_type' => $step->assignee_type?->value,
                'assignee_value' => $step->assignee_value,
                'assignee_config' => $step->assignee_config,
                'form_schema' => $step->form_schema ?? [],
                'action_schema' => $step->action_schema,
                'sla_hours' => $step->sla_hours,
                'allow_reassign' => $step->allow_reassign,
                'allow_delegate' => $step->allow_delegate,
                'is_initial' => $step->is_initial,
                'is_terminal' => $step->is_terminal,
                'sort_order' => $step->sort_order,
                'canvas_position' => $step->canvas_position,
            ])
            ->values()
            ->all();

        // Build uuid → id map for transition resolution
        // id => uuid lookup for resolving transition references
        $idToUuid = $workflow->steps()->pluck('uuid', 'id')->all();

        $transitions = $workflow->transitions()
            ->orderBy('priority')
            ->get()
            ->map(fn (WorkflowTransition $t) => [
                'from_uuid' => $idToUuid[$t->from_step_id] ?? null,
                'to_uuid' => $t->to_step_id !== null ? ($idToUuid[$t->to_step_id] ?? null) : null,
                'action_name' => $t->action_name,
                'rule_type' => $t->rule_type?->value,
                'condition_rules' => $t->condition_rules ?? [],
                'priority' => $t->priority,
                'is_default' => $t->is_default,
                'transition_meta' => $t->transition_meta,
            ])
            ->values()
            ->all();

        $automatedActions = $workflow->automatedActions()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($action) use ($idToUuid) {
                $stepUuid = $action->step_id !== null ? ($idToUuid[$action->step_id] ?? null) : null;

                return [
                    'step_uuid' => $stepUuid,
                    'trigger_event' => $action->trigger_event?->value,
                    'action_type' => $action->action_type?->value,
                    'name' => $action->name,
                    'config' => $action->config,
                    'is_active' => $action->is_active,
                    'sort_order' => $action->sort_order,
                ];
            })
            ->values()
            ->all();

        return [
            'schema_version' => self::SCHEMA_VERSION,
            'exported_at' => now()->toIso8601String(),
            'workflow' => [
                'code' => $workflow->code,
                'name' => $workflow->name,
                'description' => $workflow->description,
                'module' => $workflow->module,
                'subject_type' => $workflow->subject_type,
                'trigger_mode' => $workflow->trigger_mode?->value,
            ],
            'steps' => $steps,
            'transitions' => $transitions,
            'automated_actions' => $automatedActions,
        ];
    }

    /**
     * Import a workflow definition as a new draft in the given tenant.
     * Regenerates all UUIDs and remaps transition references.
     */
    public function import(array $payload, int $tenantId, ?int $organizationId = null): Workflow
    {
        $errors = $this->validatePayload($payload);
        if (! empty($errors)) {
            throw new \InvalidArgumentException('Invalid workflow payload: '.implode('; ', $errors));
        }

        return DB::transaction(function () use ($payload, $tenantId, $organizationId) {
            $wf = $payload['workflow'];

            $baseCode = $wf['code'] ?? 'imported_'.Str::random(6);
            $code = $baseCode;

            // Ensure unique code in tenant scope
            $attempt = 0;
            while (Workflow::query()->where('tenant_id', $tenantId)->where('code', $code)->exists()) {
                $attempt++;
                $code = $baseCode.'_'.$attempt;
            }

            $nextVersion = (int) Workflow::query()
                ->where('tenant_id', $tenantId)
                ->where('code', $code)
                ->max('version') + 1;

            $workflow = Workflow::query()->create([
                'tenant_id' => $tenantId,
                'organization_id' => $organizationId,
                'code' => $code,
                'name' => $wf['name'] ?? 'Imported Workflow',
                'description' => $wf['description'] ?? null,
                'module' => $wf['module'] ?? null,
                'subject_type' => $wf['subject_type'] ?? null,
                'trigger_mode' => $wf['trigger_mode'] ?? 'manual',
                'version' => $nextVersion,
                'status' => WorkflowDefinitionStatus::Draft,
                'is_active' => false,
            ]);

            // Map old_uuid → new WorkflowStep id
            $uuidMap = []; // old_uuid => new_step_id

            foreach ($payload['steps'] as $stepData) {
                $newStep = WorkflowStep::query()->create([
                    'workflow_id' => $workflow->id,
                    'uuid' => (string) Str::uuid(),
                    'code' => $stepData['code'] ?? Str::slug($stepData['name'] ?? 'step'),
                    'name' => $stepData['name'] ?? 'Step',
                    'description' => $stepData['description'] ?? null,
                    'step_type' => $stepData['step_type'] ?? 'task',
                    'gateway_type' => $stepData['gateway_type'] ?? 'none',
                    'quorum_strategy' => $stepData['quorum_strategy'] ?? null,
                    'quorum_value' => $stepData['quorum_value'] ?? null,
                    'assignee_type' => $stepData['assignee_type'] ?? 'user',
                    'assignee_value' => $stepData['assignee_value'] ?? null,
                    'assignee_config' => $stepData['assignee_config'] ?? null,
                    'form_schema' => $stepData['form_schema'] ?? [],
                    'action_schema' => $stepData['action_schema'] ?? null,
                    'sla_hours' => $stepData['sla_hours'] ?? null,
                    'allow_reassign' => $stepData['allow_reassign'] ?? false,
                    'allow_delegate' => $stepData['allow_delegate'] ?? false,
                    'is_initial' => $stepData['is_initial'] ?? false,
                    'is_terminal' => $stepData['is_terminal'] ?? false,
                    'sort_order' => $stepData['sort_order'] ?? 0,
                    'canvas_position' => $stepData['canvas_position'] ?? null,
                ]);

                $uuidMap[$stepData['uuid']] = $newStep->id;
            }

            foreach ($payload['transitions'] ?? [] as $t) {
                WorkflowTransition::query()->create([
                    'workflow_id' => $workflow->id,
                    'from_step_id' => $uuidMap[$t['from_uuid']] ?? null,
                    'to_step_id' => isset($t['to_uuid']) ? ($uuidMap[$t['to_uuid']] ?? null) : null,
                    'action_name' => $t['action_name'] ?? 'proceed',
                    'rule_type' => $t['rule_type'] ?? 'none',
                    'condition_rules' => $t['condition_rules'] ?? null,
                    'priority' => $t['priority'] ?? 0,
                    'is_default' => $t['is_default'] ?? false,
                    'transition_meta' => $t['transition_meta'] ?? null,
                ]);
            }

            foreach ($payload['automated_actions'] ?? [] as $action) {
                WorkflowAutomatedAction::query()->create([
                    'workflow_id' => $workflow->id,
                    'step_id' => isset($action['step_uuid']) ? ($uuidMap[$action['step_uuid']] ?? null) : null,
                    'trigger_event' => $action['trigger_event'] ?? null,
                    'action_type' => $action['action_type'] ?? null,
                    'name' => $action['name'] ?? 'Action',
                    'config' => $action['config'] ?? null,
                    'is_active' => $action['is_active'] ?? true,
                    'sort_order' => $action['sort_order'] ?? 0,
                ]);
            }

            return $workflow->fresh(['steps', 'transitions']);
        });
    }

    /**
     * Validate a workflow export payload.
     *
     * @return string[] list of error strings; empty = valid
     */
    public function validatePayload(array $payload): array
    {
        $errors = [];

        if (! isset($payload['schema_version'])) {
            $errors[] = 'Missing schema_version';
        }

        if (! isset($payload['workflow']) || ! is_array($payload['workflow'])) {
            $errors[] = 'Missing or invalid workflow definition';

            return $errors;
        }

        if (empty($payload['steps']) || ! is_array($payload['steps'])) {
            $errors[] = 'steps must be a non-empty array';

            return $errors;
        }

        $seenUuids = [];
        foreach ($payload['steps'] as $i => $step) {
            if (empty($step['uuid'])) {
                $errors[] = "Step[$i] missing uuid";

                continue;
            }
            if (in_array($step['uuid'], $seenUuids, true)) {
                $errors[] = "Step[$i] duplicate uuid: {$step['uuid']}";
            }
            $seenUuids[] = $step['uuid'];
        }

        foreach ($payload['transitions'] ?? [] as $i => $t) {
            if (empty($t['from_uuid'])) {
                $errors[] = "Transition[$i] missing from_uuid";

                continue;
            }
            if (! in_array($t['from_uuid'], $seenUuids, true)) {
                $errors[] = "Transition[$i] from_uuid '{$t['from_uuid']}' does not reference a known step";
            }
            if (! empty($t['to_uuid']) && ! in_array($t['to_uuid'], $seenUuids, true)) {
                $errors[] = "Transition[$i] to_uuid '{$t['to_uuid']}' does not reference a known step";
            }
        }

        foreach ($payload['automated_actions'] ?? [] as $i => $action) {
            if (! empty($action['step_uuid']) && ! in_array($action['step_uuid'], $seenUuids, true)) {
                $errors[] = "AutomatedAction[$i] step_uuid '{$action['step_uuid']}' does not reference a known step";
            }
        }

        return $errors;
    }
}
