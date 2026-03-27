<?php

namespace Modules\Workflow\Listeners;

use Modules\Workflow\Contracts\WorkflowAssigneeResolver;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowAssignmentCreated;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Events\WorkflowStarted;
use Modules\Workflow\Models\WorkflowAssignment;

class CreateAssignmentsForCurrentStep
{
    public function __construct(private readonly WorkflowAssigneeResolver $resolver) {}

    public function handle(WorkflowStarted|WorkflowAdvanced|WorkflowReturned $event): void
    {
        $instance = $event->instance->fresh(['currentStep', 'assignments']);
        $step = $instance?->currentStep;

        if (! $instance || ! $step || $step->is_terminal) {
            return;
        }

        $existing = $instance->assignments()
            ->where('step_id', $step->getKey())
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return;
        }

        $users = $this->resolver->resolveUsers($instance, $step);
        $currentAssignees = [];

        foreach ($users as $user) {
            $assignment = WorkflowAssignment::query()->create([
                'workflow_instance_id' => $instance->getKey(),
                'step_id' => $step->getKey(),
                'assigned_to_type' => 'user',
                'assigned_to_id' => $user->getKey(),
                'assignment_role' => $step->assignee_type?->value,
                'status' => 'pending',
                'assigned_at' => now(),
                'due_at' => $instance->due_at,
                'meta' => [
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ],
            ]);

            $currentAssignees[] = [
                'id' => $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
            ];

            WorkflowAssignmentCreated::dispatch($assignment);
        }

        $instance->forceFill([
            'current_assignees' => $currentAssignees,
        ])->save();
    }
}
