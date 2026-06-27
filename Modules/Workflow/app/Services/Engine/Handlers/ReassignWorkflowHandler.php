<?php

namespace Modules\Workflow\Services\Engine\Handlers;

use Modules\Core\Models\User;
use Modules\Workflow\Contracts\WorkflowAuditLogger;
use Modules\Workflow\Enums\WorkflowAssignmentStatus;
use Modules\Workflow\Enums\WorkflowLogType;
use Modules\Workflow\Models\WorkflowAssignment;
use Modules\Workflow\Services\Engine\Support\ActorAuthorizer;
use Modules\Workflow\Services\Engine\Support\WorkflowInstanceLocker;

class ReassignWorkflowHandler
{
    public function __construct(
        private readonly WorkflowAuditLogger $auditLogger,
        private readonly ActorAuthorizer $actorAuthorizer,
        private readonly WorkflowInstanceLocker $instanceLocker,
    ) {}

    public function handle(
        WorkflowAssignment $assignment,
        User $actor,
        User $targetUser,
        ?string $reason = null,
    ): WorkflowAssignment {
        $assignment = $assignment->fresh(['instance', 'step']);

        $instance = $this->instanceLocker->lock(
            $assignment->instance,
            ['assignments', 'currentStep'],
        );

        $assignment->setRelation('instance', $instance);

        $this->actorAuthorizer->authorize($assignment->instance, $actor);

        $assignment->forceFill([
            'status' => WorkflowAssignmentStatus::Cancelled,
            'completed_at' => now(),
        ])->save();

        $newAssignment = WorkflowAssignment::query()->create([
            'workflow_instance_id' => $assignment->workflow_instance_id,
            'step_id' => $assignment->step_id,
            'assigned_to_type' => 'user',
            'assigned_to_id' => $targetUser->getKey(),
            'assignment_role' => $assignment->assignment_role,
            'status' => WorkflowAssignmentStatus::Pending,
            'assigned_at' => now(),
            'due_at' => $assignment->due_at,
            'meta' => array_merge($assignment->meta ?? [], [
                'reassigned_by' => $actor->getKey(),
                'reassign_reason' => $reason,
            ]),
        ]);

        $assignment->instance->forceFill([
            'current_assignees' => [[
                'id' => $targetUser->getKey(),
                'name' => $targetUser->name,
                'email' => $targetUser->email,
            ]],
        ])->save();

        $this->auditLogger->log($assignment->instance->fresh(), WorkflowLogType::Reassigned->value, [
            'step_id' => $assignment->step_id,
            'actor_id' => $actor->getKey(),
            'action_taken' => 'reassign',
            'notes' => $reason,
        ]);

        return $newAssignment;
    }
}
