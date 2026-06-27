<?php

namespace Modules\Workflow\Services;

use App\Support\TypedValue;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;

class WorkflowSnapshotStepResolver
{
    public function resolveCurrent(WorkflowInstance $instance): ?WorkflowStep
    {
        if ($instance->current_step_id === null) {
            return null;
        }

        return $this->materialize($instance, (int) $instance->current_step_id);
    }

    public function materialize(WorkflowInstance $instance, int $stepId): ?WorkflowStep
    {
        /** @var list<array<string, mixed>> $steps */
        $steps = data_get($instance->workflow_snapshot, 'steps', []);
        $snapshotStep = collect($steps)
            ->first(fn (array $step): bool => TypedValue::int($step['id'] ?? 0) === $stepId);

        if (is_array($snapshotStep) && $snapshotStep !== []) {
            return $this->hydrateStep($snapshotStep, $stepId);
        }

        return WorkflowStep::query()->find($stepId);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function hydrateStep(array $attributes, int $stepId): WorkflowStep
    {
        $step = new WorkflowStep($attributes);
        $step->exists = true;
        $step->setAttribute($step->getKeyName(), $stepId);

        return $step;
    }
}
