<?php

namespace Modules\Workflow\Services;

use App\Support\TypedValue;
use Illuminate\Support\Collection;
use Modules\Workflow\Contracts\RuleEngine;
use Modules\Workflow\Contracts\WorkflowTransitionResolver;
use Modules\Workflow\Exceptions\NoValidWorkflowTransitionException;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;

class JsonLogicWorkflowTransitionResolver implements WorkflowTransitionResolver
{
    public function __construct(
        private readonly RuleEngine $ruleEngine,
        private readonly WorkflowSnapshotStepResolver $snapshotStepResolver,
    ) {}

    /**
     * @param  array<string, mixed>  $incomingData
     */
    public function resolve(WorkflowInstance $instance, WorkflowStep $step, string $actionName, array $incomingData): WorkflowTransition
    {
        /** @var list<array<string, mixed>> $transitionRows */
        $transitionRows = data_get($instance->workflow_snapshot, 'transitions', []);
        $snapshotTransitions = collect($transitionRows);

        if ($snapshotTransitions->isNotEmpty()) {
            $fromSnapshot = $this->resolveFromSnapshot(
                $instance,
                $snapshotTransitions,
                $step->getKey(),
                $actionName,
                $incomingData,
            );

            if ($fromSnapshot !== null) {
                return $fromSnapshot;
            }

            throw new NoValidWorkflowTransitionException(
                "No valid workflow transition found in snapshot for action [{$actionName}] on step [{$step->code}].",
            );
        }

        return $this->resolveFromLiveDefinition($step, $actionName, $incomingData);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $snapshotTransitions
     * @param  array<string, mixed>  $incomingData
     */
    protected function resolveFromSnapshot(
        WorkflowInstance $instance,
        Collection $snapshotTransitions,
        int|string $fromStepId,
        string $actionName,
        array $incomingData,
    ): ?WorkflowTransition {
        $candidates = $snapshotTransitions
            ->filter(fn (array $transition): bool => TypedValue::int($transition['from_step_id'] ?? 0) === TypedValue::int($fromStepId)
                && TypedValue::string($transition['action_name'] ?? '') === $actionName)
            ->sort(function (array $left, array $right): int {
                $priority = (TypedValue::int($right['priority'] ?? 0)) <=> (TypedValue::int($left['priority'] ?? 0));

                if ($priority !== 0) {
                    return $priority;
                }

                return ((bool) ($right['is_default'] ?? false)) <=> ((bool) ($left['is_default'] ?? false));
            })
            ->values();

        foreach ($candidates as $snapshot) {
            $rules = $snapshot['condition_rules'] ?? null;

            if (empty($rules)) {
                return $this->materializeTransition($instance, $snapshot);
            }

            if (is_array($rules) && $this->ruleEngine->matches($rules, $incomingData)) {
                return $this->materializeTransition($instance, $snapshot);
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    protected function materializeTransition(WorkflowInstance $instance, array $snapshot): WorkflowTransition
    {
        $transitionId = $snapshot['id'] ?? null;
        $transition = is_scalar($transitionId)
            ? WorkflowTransition::query()->find($transitionId)
            : null;

        if (! $transition instanceof WorkflowTransition) {
            $transition = new WorkflowTransition($snapshot);
            $transition->exists = isset($snapshot['id']);

            if (isset($snapshot['id'])) {
                $transition->setAttribute($transition->getKeyName(), $snapshot['id']);
            }
        }

        if (isset($snapshot['to_step_id'])) {
            $toStep = $this->snapshotStepResolver->materialize($instance, TypedValue::int($snapshot['to_step_id']));
            if ($toStep !== null) {
                $transition->setRelation('toStep', $toStep);
            }
        }

        return $transition;
    }

    /**
     * @param  array<string, mixed>  $incomingData
     */
    protected function resolveFromLiveDefinition(WorkflowStep $step, string $actionName, array $incomingData): WorkflowTransition
    {
        $candidates = WorkflowTransition::query()
            ->where('from_step_id', $step->getKey())
            ->where('action_name', $actionName)
            ->orderByDesc('priority')
            ->orderByDesc('is_default')
            ->get();

        foreach ($candidates as $transition) {
            if (empty($transition->condition_rules)) {
                return $transition;
            }

            if ($this->ruleEngine->matches($transition->condition_rules ?? [], $incomingData)) {
                return $transition;
            }
        }

        throw new NoValidWorkflowTransitionException("No valid workflow transition found for action [{$actionName}] on step [{$step->code}].");
    }
}
