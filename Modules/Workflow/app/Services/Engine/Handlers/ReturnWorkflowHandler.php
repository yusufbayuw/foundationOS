<?php

namespace Modules\Workflow\Services\Engine\Handlers;

use Modules\Core\Models\User;
use Modules\Workflow\Contracts\WorkflowAuditLogger;
use Modules\Workflow\Contracts\WorkflowFormSchemaValidator;
use Modules\Workflow\Contracts\WorkflowSlaService;
use Modules\Workflow\Enums\WorkflowAssignmentStatus;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Enums\WorkflowLogType;
use Modules\Workflow\Exceptions\WorkflowAuthorizationException;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Services\Engine\Support\ActorAuthorizer;
use Modules\Workflow\Services\Engine\Support\PayloadSnapshot;
use Modules\Workflow\Services\WorkflowSnapshotStepResolver;
use Modules\Workflow\Support\WorkflowContextData;

class ReturnWorkflowHandler
{
    public function __construct(
        private readonly WorkflowFormSchemaValidator $validator,
        private readonly WorkflowAuditLogger $auditLogger,
        private readonly WorkflowSlaService $slaService,
        private readonly WorkflowSnapshotStepResolver $snapshotStepResolver,
        private readonly ActorAuthorizer $actorAuthorizer,
        private readonly PayloadSnapshot $payloadSnapshot,
    ) {}

    public function handle(
        WorkflowInstance $instance,
        int $targetStepId,
        array $formData,
        User $actor,
        ?string $notes = null,
    ): WorkflowInstance {
        $this->actorAuthorizer->authorize($instance, $actor);
        $statusBefore = $instance->status->value;

        $targetStep = $this->snapshotStepResolver->materialize($instance, $targetStepId);

        if (! $targetStep) {
            throw new WorkflowAuthorizationException('Target return step is not part of the workflow.');
        }

        $currentStep = $this->snapshotStepResolver->resolveCurrent($instance);
        $incomingContext = WorkflowContextData::fromInstance($instance, $formData);
        $validated = $this->validator->validate($currentStep, $formData, $incomingContext);
        $payloadBefore = $this->payloadSnapshot->capture($instance);

        $instance->assignments()
            ->where('status', WorkflowAssignmentStatus::Pending)
            ->update([
                'status' => WorkflowAssignmentStatus::Cancelled->value,
                'updated_at' => now(),
            ]);

        $instance->forceFill([
            'current_step_id' => $targetStep->getKey(),
            'form_data' => array_replace_recursive($instance->form_data ?? [], $validated),
            'status' => WorkflowInstanceStatus::Running,
            'current_assignees' => [],
            'due_at' => $this->slaService->computeDueAt($targetStep, now()),
            'completed_at' => null,
            'rejected_at' => null,
            'cancelled_at' => null,
        ])->save();

        $this->auditLogger->log($instance, WorkflowLogType::Returned->value, [
            'step_id' => $targetStep->getKey(),
            'actor_id' => $actor->getKey(),
            'action_taken' => 'return',
            'status_before' => $statusBefore,
            'status_after' => $instance->status->value,
            'payload_before' => $payloadBefore,
            'payload_after' => $this->payloadSnapshot->capture($instance),
            'form_data_snapshot' => $validated,
            'notes' => $notes,
        ]);

        return $instance->fresh(['currentStep', 'assignments', 'logs']);
    }
}
