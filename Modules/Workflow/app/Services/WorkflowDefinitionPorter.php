<?php

namespace Modules\Workflow\Services;

use App\Support\TypedValue;
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
     *
     * @return array<string, mixed>
     */
    public function export(Workflow $workflow): array
    {
        $workflow = $workflow->fresh(['steps', 'transitions', 'automatedActions']);

        if ($workflow === null) {
            throw new \RuntimeException('Workflow definition could not be loaded.');
        }

        $steps = $workflow->steps()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (WorkflowStep $step) => [
                'uuid' => $step->uuid,
                'code' => $step->code,
                'name' => $step->name,
                'description' => $step->description,
                'step_type' => $step->step_type->value,
                'gateway_type' => $step->gateway_type->value,
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

        $idToUuid = $workflow->steps()->pluck('uuid', 'id')->all();

        $transitions = $workflow->transitions()
            ->orderBy('priority')
            ->get()
            ->map(fn (WorkflowTransition $t) => [
                'from_uuid' => $idToUuid[$t->from_step_id] ?? null,
                'to_uuid' => $t->to_step_id !== null ? ($idToUuid[$t->to_step_id] ?? null) : null,
                'action_name' => $t->action_name,
                'rule_type' => $t->rule_type->value,
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
            ->map(function (WorkflowAutomatedAction $action) use ($idToUuid) {
                $stepUuid = $action->step_id !== null ? ($idToUuid[$action->step_id] ?? null) : null;

                return [
                    'step_uuid' => $stepUuid,
                    'trigger_event' => $action->trigger_event->value,
                    'action_type' => $action->action_type->value,
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
                'trigger_mode' => $workflow->trigger_mode->value,
            ],
            'steps' => $steps,
            'transitions' => $transitions,
            'automated_actions' => $automatedActions,
        ];
    }

    /**
     * Import a workflow definition as a new draft in the given tenant.
     * Regenerates all UUIDs and remaps transition references.
     *
     * @param  array<string, mixed>  $payload
     */
    public function import(array $payload, int $tenantId, ?int $organizationId = null): Workflow
    {
        $errors = $this->validatePayload($payload);
        if ($errors !== []) {
            throw new \InvalidArgumentException('Invalid workflow payload: '.implode('; ', $errors));
        }

        $imported = DB::transaction(function () use ($payload, $tenantId, $organizationId): Workflow {
            /** @var array<string, mixed> $wf */
            $wf = $payload['workflow'];

            $baseCode = TypedValue::string($wf['code'] ?? null, 'imported_'.Str::random(6));
            $code = $baseCode;

            $attempt = 0;
            while (Workflow::query()->where('tenant_id', $tenantId)->where('code', $code)->exists()) {
                $attempt++;
                $code = $baseCode.'_'.$attempt;
            }

            $nextVersion = TypedValue::int(
                Workflow::query()
                    ->where('tenant_id', $tenantId)
                    ->where('code', $code)
                    ->max('version'),
            ) + 1;

            $workflow = Workflow::query()->create([
                'tenant_id' => $tenantId,
                'organization_id' => $organizationId,
                'code' => $code,
                'name' => TypedValue::string($wf['name'] ?? null, 'Imported Workflow'),
                'description' => is_string($wf['description'] ?? null) ? $wf['description'] : null,
                'module' => is_string($wf['module'] ?? null) ? $wf['module'] : null,
                'subject_type' => is_string($wf['subject_type'] ?? null) ? $wf['subject_type'] : null,
                'trigger_mode' => TypedValue::string($wf['trigger_mode'] ?? null, 'manual'),
                'version' => $nextVersion,
                'status' => WorkflowDefinitionStatus::Draft,
                'is_active' => false,
            ]);

            /** @var array<string, int> $uuidMap */
            $uuidMap = [];

            /** @var list<array<string, mixed>> $steps */
            $steps = $payload['steps'];

            foreach ($steps as $stepData) {
                $newStep = WorkflowStep::query()->create([
                    'workflow_id' => $workflow->id,
                    'uuid' => (string) Str::uuid(),
                    'code' => TypedValue::string($stepData['code'] ?? null, Str::slug(TypedValue::string($stepData['name'] ?? null, 'step'))),
                    'name' => TypedValue::string($stepData['name'] ?? null, 'Step'),
                    'description' => is_string($stepData['description'] ?? null) ? $stepData['description'] : null,
                    'step_type' => TypedValue::string($stepData['step_type'] ?? null, 'task'),
                    'gateway_type' => TypedValue::string($stepData['gateway_type'] ?? null, 'none'),
                    'quorum_strategy' => is_string($stepData['quorum_strategy'] ?? null) ? $stepData['quorum_strategy'] : null,
                    'quorum_value' => $stepData['quorum_value'] ?? null,
                    'assignee_type' => TypedValue::string($stepData['assignee_type'] ?? null, 'user'),
                    'assignee_value' => is_string($stepData['assignee_value'] ?? null) ? $stepData['assignee_value'] : null,
                    'assignee_config' => is_array($stepData['assignee_config'] ?? null) ? $stepData['assignee_config'] : null,
                    'form_schema' => is_array($stepData['form_schema'] ?? null) ? $stepData['form_schema'] : [],
                    'action_schema' => is_array($stepData['action_schema'] ?? null) ? $stepData['action_schema'] : null,
                    'sla_hours' => $stepData['sla_hours'] ?? null,
                    'allow_reassign' => (bool) ($stepData['allow_reassign'] ?? false),
                    'allow_delegate' => (bool) ($stepData['allow_delegate'] ?? false),
                    'is_initial' => (bool) ($stepData['is_initial'] ?? false),
                    'is_terminal' => (bool) ($stepData['is_terminal'] ?? false),
                    'sort_order' => TypedValue::int($stepData['sort_order'] ?? null),
                    'canvas_position' => is_array($stepData['canvas_position'] ?? null) ? $stepData['canvas_position'] : null,
                ]);

                $uuidMap[TypedValue::string($stepData['uuid'] ?? null)] = $newStep->id;
            }

            /** @var list<array<string, mixed>> $transitions */
            $transitions = is_array($payload['transitions'] ?? null) ? $payload['transitions'] : [];

            foreach ($transitions as $t) {
                WorkflowTransition::query()->create([
                    'workflow_id' => $workflow->id,
                    'from_step_id' => $uuidMap[TypedValue::string($t['from_uuid'] ?? null)] ?? null,
                    'to_step_id' => isset($t['to_uuid']) ? ($uuidMap[TypedValue::string($t['to_uuid'])] ?? null) : null,
                    'action_name' => TypedValue::string($t['action_name'] ?? null, 'proceed'),
                    'rule_type' => TypedValue::string($t['rule_type'] ?? null, 'none'),
                    'condition_rules' => is_array($t['condition_rules'] ?? null) ? $t['condition_rules'] : null,
                    'priority' => TypedValue::int($t['priority'] ?? null),
                    'is_default' => (bool) ($t['is_default'] ?? false),
                    'transition_meta' => is_array($t['transition_meta'] ?? null) ? $t['transition_meta'] : null,
                ]);
            }

            /** @var list<array<string, mixed>> $automatedActions */
            $automatedActions = is_array($payload['automated_actions'] ?? null) ? $payload['automated_actions'] : [];

            foreach ($automatedActions as $action) {
                WorkflowAutomatedAction::query()->create([
                    'workflow_id' => $workflow->id,
                    'step_id' => isset($action['step_uuid']) ? ($uuidMap[TypedValue::string($action['step_uuid'])] ?? null) : null,
                    'trigger_event' => TypedValue::string($action['trigger_event'] ?? null, 'step_entered'),
                    'action_type' => TypedValue::string($action['action_type'] ?? null, 'notify'),
                    'name' => TypedValue::string($action['name'] ?? null, 'Action'),
                    'config' => is_array($action['config'] ?? null) ? $action['config'] : null,
                    'is_active' => (bool) ($action['is_active'] ?? true),
                    'sort_order' => TypedValue::int($action['sort_order'] ?? null),
                ]);
            }

            $fresh = $workflow->fresh(['steps', 'transitions']);

            if ($fresh === null) {
                throw new \RuntimeException('Imported workflow could not be loaded.');
            }

            return $fresh;
        });

        return $imported;
    }

    /**
     * Validate a workflow export payload.
     *
     * @param  array<string, mixed>  $payload
     * @return list<string>
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
        /** @var list<array<string, mixed>> $steps */
        $steps = $payload['steps'];

        foreach ($steps as $i => $step) {
            $uuid = TypedValue::string($step['uuid'] ?? null);
            if ($uuid === '') {
                $errors[] = "Step[{$i}] missing uuid";

                continue;
            }
            if (in_array($uuid, $seenUuids, true)) {
                $errors[] = "Step[{$i}] duplicate uuid: {$uuid}";
            }
            $seenUuids[] = $uuid;
        }

        /** @var list<array<string, mixed>> $transitions */
        $transitions = is_array($payload['transitions'] ?? null) ? $payload['transitions'] : [];

        foreach ($transitions as $i => $t) {
            $fromUuid = TypedValue::string($t['from_uuid'] ?? null);
            if ($fromUuid === '') {
                $errors[] = "Transition[{$i}] missing from_uuid";

                continue;
            }
            if (! in_array($fromUuid, $seenUuids, true)) {
                $errors[] = "Transition[{$i}] from_uuid '{$fromUuid}' does not reference a known step";
            }
            $toUuid = TypedValue::string($t['to_uuid'] ?? null);
            if ($toUuid !== '' && ! in_array($toUuid, $seenUuids, true)) {
                $errors[] = "Transition[{$i}] to_uuid '{$toUuid}' does not reference a known step";
            }
        }

        /** @var list<array<string, mixed>> $automatedActions */
        $automatedActions = is_array($payload['automated_actions'] ?? null) ? $payload['automated_actions'] : [];

        foreach ($automatedActions as $i => $action) {
            $stepUuid = TypedValue::string($action['step_uuid'] ?? null);
            if ($stepUuid !== '' && ! in_array($stepUuid, $seenUuids, true)) {
                $errors[] = "AutomatedAction[{$i}] step_uuid '{$stepUuid}' does not reference a known step";
            }
        }

        return $errors;
    }
}
