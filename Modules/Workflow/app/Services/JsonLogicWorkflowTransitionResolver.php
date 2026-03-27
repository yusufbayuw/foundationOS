<?php

namespace Modules\Workflow\Services;

use Modules\Workflow\Contracts\RuleEngine;
use Modules\Workflow\Contracts\WorkflowTransitionResolver;
use Modules\Workflow\Exceptions\NoValidWorkflowTransitionException;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
class JsonLogicWorkflowTransitionResolver implements WorkflowTransitionResolver
{
    public function __construct(private readonly RuleEngine $ruleEngine) {}

    public function resolve(WorkflowInstance $instance, WorkflowStep $step, string $actionName, array $incomingData): WorkflowTransition
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
