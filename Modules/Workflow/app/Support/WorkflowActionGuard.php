<?php

namespace Modules\Workflow\Support;

use Modules\Workflow\Exceptions\WorkflowAuthorizationException;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;

class WorkflowActionGuard
{
    /**
     * @return list<string>
     */
    public function allowedAdvanceActionNames(WorkflowInstance $instance, ?WorkflowStep $currentStep): array
    {
        $configured = collect($currentStep?->action_schema ?? [])
            ->pluck('name')
            ->filter(fn (mixed $name): bool => is_string($name) && $name !== '')
            ->values()
            ->all();

        $transitionActions = collect(data_get($instance->workflow_snapshot, 'transitions', []))
            ->where('from_step_id', $instance->current_step_id)
            ->pluck('action_name')
            ->filter(fn (mixed $name): bool => is_string($name) && $name !== '')
            ->values()
            ->all();

        return array_values(array_unique(array_merge($configured, $transitionActions)));
    }

    public function isAdvanceActionAllowed(WorkflowInstance $instance, ?WorkflowStep $currentStep, string $actionName): bool
    {
        return in_array($actionName, $this->allowedAdvanceActionNames($instance, $currentStep), true);
    }

    public function assertAdvanceActionAllowed(WorkflowInstance $instance, ?WorkflowStep $currentStep, string $actionName): void
    {
        if (! $this->isAdvanceActionAllowed($instance, $currentStep, $actionName)) {
            throw new WorkflowAuthorizationException(
                "Action [{$actionName}] is not permitted for the current workflow step.",
            );
        }
    }
}
