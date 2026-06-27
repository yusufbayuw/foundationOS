<?php

namespace Modules\Workflow\Listeners;

use Modules\Workflow\Contracts\WorkflowAssigneeResolver;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowAssignmentCreated;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Events\WorkflowStarted;
use Modules\Workflow\Services\WorkflowEscalationService;
use Modules\Workflow\Services\WorkflowSnapshotStepResolver;

class CreateAssignmentsForCurrentStep
{
    public function __construct(
        private readonly WorkflowAssigneeResolver $resolver,
        private readonly WorkflowSnapshotStepResolver $snapshotStepResolver,
        private readonly WorkflowEscalationService $escalationService,
    ) {}

    public function handle(WorkflowStarted|WorkflowAdvanced|WorkflowReturned $event): void
    {
        $instance = $event->instance->fresh(['assignments']);
        $step = $instance ? $this->snapshotStepResolver->resolveCurrent($instance) : null;

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
            $assignment = $this->escalationService->createAssignmentWithDelegation($instance, $step, $user);
            $assignment->forceFill([
                'assignment_role' => $step->assignee_type->value ?? $assignment->assignment_role,
                'due_at' => $instance->due_at ?? $assignment->due_at,
                'meta' => array_merge($assignment->meta ?? [], [
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ]),
            ])->save();

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
