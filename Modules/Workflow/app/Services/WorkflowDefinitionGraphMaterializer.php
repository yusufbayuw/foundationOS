<?php

namespace Modules\Workflow\Services;

use Illuminate\Support\Str;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowAutomatedAction;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;

class WorkflowDefinitionGraphMaterializer
{
    /**
     * @param  array<int, array<string, mixed>>  $steps
     * @return array<string, int> exported uuid => new step id
     */
    public function createStepsFromPayload(Workflow $workflow, array $steps): array
    {
        $uuidMap = [];

        foreach ($steps as $stepData) {
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

        return $uuidMap;
    }

    /**
     * @param  array<int, array<string, mixed>>  $transitions
     * @param  array<string, int>  $uuidMap
     */
    public function createTransitionsFromPayload(Workflow $workflow, array $transitions, array $uuidMap): void
    {
        foreach ($transitions as $transition) {
            WorkflowTransition::query()->create([
                'workflow_id' => $workflow->id,
                'from_step_id' => $uuidMap[$transition['from_uuid']] ?? null,
                'to_step_id' => isset($transition['to_uuid']) ? ($uuidMap[$transition['to_uuid']] ?? null) : null,
                'action_name' => $transition['action_name'] ?? 'proceed',
                'rule_type' => $transition['rule_type'] ?? 'none',
                'condition_rules' => $transition['condition_rules'] ?? null,
                'priority' => $transition['priority'] ?? 0,
                'is_default' => $transition['is_default'] ?? false,
                'transition_meta' => $transition['transition_meta'] ?? null,
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $actions
     * @param  array<string, int>  $uuidMap
     */
    public function createAutomatedActionsFromPayload(Workflow $workflow, array $actions, array $uuidMap): void
    {
        foreach ($actions as $action) {
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
    }

    /**
     * @return array<int, int> source step id => cloned step id
     */
    public function copyStepsFromWorkflow(Workflow $source, Workflow $target): array
    {
        $stepIdMap = [];

        foreach ($source->steps()->orderBy('sort_order')->get() as $step) {
            $newStep = WorkflowStep::query()->create([
                'workflow_id' => $target->id,
                'uuid' => (string) Str::uuid(),
                'code' => $step->code,
                'name' => $step->name,
                'description' => $step->description,
                'step_type' => $step->step_type,
                'assignee_type' => $step->assignee_type,
                'assignee_value' => $step->assignee_value,
                'assignee_config' => $step->assignee_config,
                'form_schema' => $step->form_schema,
                'action_schema' => $step->action_schema,
                'sla_hours' => $step->sla_hours,
                'allow_reassign' => $step->allow_reassign,
                'allow_delegate' => $step->allow_delegate,
                'is_initial' => $step->is_initial,
                'is_terminal' => $step->is_terminal,
                'sort_order' => $step->sort_order,
                'canvas_position' => $step->canvas_position,
            ]);

            $stepIdMap[$step->id] = $newStep->id;
        }

        return $stepIdMap;
    }

    /**
     * @param  array<int, int>  $stepIdMap
     */
    public function copyTransitionsFromWorkflow(Workflow $source, Workflow $target, array $stepIdMap): void
    {
        foreach ($source->transitions as $transition) {
            WorkflowTransition::query()->create([
                'workflow_id' => $target->id,
                'from_step_id' => $stepIdMap[$transition->from_step_id] ?? $transition->from_step_id,
                'to_step_id' => $transition->to_step_id ? ($stepIdMap[$transition->to_step_id] ?? $transition->to_step_id) : null,
                'action_name' => $transition->action_name,
                'rule_type' => $transition->rule_type,
                'condition_rules' => $transition->condition_rules,
                'priority' => $transition->priority,
                'is_default' => $transition->is_default,
                'transition_meta' => $transition->transition_meta,
            ]);
        }
    }

    /**
     * @param  array<int, int>  $stepIdMap
     */
    public function copyAutomatedActionsFromWorkflow(Workflow $source, Workflow $target, array $stepIdMap): void
    {
        foreach ($source->automatedActions as $action) {
            WorkflowAutomatedAction::query()->create([
                'workflow_id' => $target->id,
                'step_id' => $action->step_id ? ($stepIdMap[$action->step_id] ?? $action->step_id) : null,
                'trigger_event' => $action->trigger_event,
                'action_type' => $action->action_type,
                'name' => $action->name,
                'config' => $action->config,
                'is_active' => $action->is_active,
                'sort_order' => $action->sort_order,
            ]);
        }
    }
}
