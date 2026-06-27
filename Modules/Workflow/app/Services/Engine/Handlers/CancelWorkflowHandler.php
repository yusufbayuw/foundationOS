<?php

namespace Modules\Workflow\Services\Engine\Handlers;

use Modules\Core\Models\User;
use Modules\Workflow\Contracts\WorkflowAuditLogger;
use Modules\Workflow\Enums\WorkflowAssignmentStatus;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Enums\WorkflowLogType;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Services\Engine\Support\ActorAuthorizer;
use Modules\Workflow\Services\Engine\Support\PayloadSnapshot;

class CancelWorkflowHandler
{
    public function __construct(
        private readonly WorkflowAuditLogger $auditLogger,
        private readonly ActorAuthorizer $actorAuthorizer,
        private readonly PayloadSnapshot $payloadSnapshot,
    ) {}

    public function handle(WorkflowInstance $instance, User $actor, ?string $reason = null): WorkflowInstance
    {
        $this->actorAuthorizer->authorize($instance, $actor);
        $statusBefore = $instance->status->value;
        $payloadBefore = $this->payloadSnapshot->capture($instance);

        $instance->assignments()
            ->where('status', WorkflowAssignmentStatus::Pending)
            ->update([
                'status' => WorkflowAssignmentStatus::Cancelled->value,
                'updated_at' => now(),
            ]);

        $instance->forceFill([
            'status' => WorkflowInstanceStatus::Cancelled,
            'current_step_id' => null,
            'current_assignees' => [],
            'cancelled_at' => now(),
            'due_at' => null,
        ])->save();

        $this->auditLogger->log($instance, WorkflowLogType::Cancelled->value, [
            'actor_id' => $actor->getKey(),
            'action_taken' => 'cancel',
            'status_before' => $statusBefore,
            'status_after' => $instance->status->value,
            'payload_before' => $payloadBefore,
            'payload_after' => $this->payloadSnapshot->capture($instance),
            'notes' => $reason,
        ]);

        return $instance->fresh(['currentStep', 'assignments', 'logs']);
    }
}
