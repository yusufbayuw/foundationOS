<?php

namespace Modules\Workflow\Services\Engine\Handlers;

use Modules\Core\Models\User;
use Modules\Workflow\Contracts\WorkflowAuditLogger;
use Modules\Workflow\Contracts\WorkflowFormSchemaValidator;
use Modules\Workflow\Contracts\WorkflowSlaService;
use Modules\Workflow\Contracts\WorkflowTransitionResolver;
use Modules\Workflow\Enums\WorkflowAssignmentStatus;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Enums\WorkflowLogType;
use Modules\Workflow\Exceptions\WorkflowEvidenceRequiredException;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Modules\Workflow\Services\Engine\Support\ActorAuthorizer;
use Modules\Workflow\Services\Engine\Support\PayloadSnapshot;
use Modules\Workflow\Services\Engine\Support\StatusResolver;
use Modules\Workflow\Services\WorkflowParallelCoordinator;
use Modules\Workflow\Services\WorkflowSnapshotStepResolver;
use Modules\Workflow\Support\WorkflowContextData;

class AdvanceWorkflowHandler
{
    public function __construct(
        private readonly WorkflowFormSchemaValidator $validator,
        private readonly WorkflowTransitionResolver $transitionResolver,
        private readonly WorkflowAuditLogger $auditLogger,
        private readonly WorkflowSlaService $slaService,
        private readonly WorkflowParallelCoordinator $parallelCoordinator,
        private readonly WorkflowSnapshotStepResolver $snapshotStepResolver,
        private readonly ActorAuthorizer $actorAuthorizer,
        private readonly StatusResolver $statusResolver,
        private readonly PayloadSnapshot $payloadSnapshot,
    ) {}

    /**
     * @return array{advanced: bool, transition: WorkflowTransition|null}
     */
    public function handle(
        WorkflowInstance $instance,
        string $actionName,
        array $formData,
        User $actor,
        ?string $notes = null,
    ): array {
        $this->actorAuthorizer->authorize($instance, $actor);

        $currentStep = $this->snapshotStepResolver->resolveCurrent($instance);

        if ($currentStep?->requiresEvidence()) {
            $uploaded = $instance->evidences()
                ->where('workflow_step_id', $currentStep->getKey())
                ->count();

            if ($uploaded < $currentStep->requiredEvidenceCount()) {
                throw new WorkflowEvidenceRequiredException(
                    $currentStep->name,
                    $currentStep->requiredEvidenceCount(),
                    $uploaded,
                );
            }
        }

        $currentStepId = $instance->current_step_id;
        $statusBefore = $instance->status->value;
        $incomingContext = WorkflowContextData::fromInstance($instance, $formData);
        $validated = $this->validator->validate($currentStep, $formData, $incomingContext);
        $payloadBefore = $this->payloadSnapshot->capture($instance);
        $mergedFormData = array_replace_recursive($instance->form_data ?? [], $validated);

        if ($currentStep && $this->parallelCoordinator->isParallel($currentStep)) {
            return $this->advanceParallel(
                $instance,
                $currentStep,
                $actor,
                $actionName,
                $validated,
                $mergedFormData,
                $statusBefore,
                $payloadBefore,
                $notes,
                $currentStepId,
            );
        }

        return $this->advanceLinear(
            $instance,
            $currentStep,
            $actor,
            $actionName,
            $validated,
            $mergedFormData,
            $statusBefore,
            $payloadBefore,
            $notes,
            $currentStepId,
        );
    }

    /**
     * @return array{advanced: bool, transition: WorkflowTransition|null}
     */
    protected function advanceLinear(
        WorkflowInstance $instance,
        ?WorkflowStep $currentStep,
        User $actor,
        string $actionName,
        array $validated,
        array $mergedFormData,
        string $statusBefore,
        array $payloadBefore,
        ?string $notes,
        ?int $currentStepId,
    ): array {
        $incoming = WorkflowContextData::fromInstance($instance, $mergedFormData);
        $transition = $this->transitionResolver->resolve($instance, $currentStep, $actionName, $incoming);

        $nextStep = $transition->toStep;
        $nextStatus = $this->statusResolver->resolve($actionName, $nextStep);

        $instance->assignments()
            ->where('step_id', $instance->current_step_id)
            ->where('status', WorkflowAssignmentStatus::Pending)
            ->update([
                'status' => WorkflowAssignmentStatus::Completed->value,
                'outcome' => $actionName,
                'completed_at' => now(),
                'updated_at' => now(),
            ]);

        $instance->forceFill([
            'current_step_id' => $nextStep?->getKey(),
            'form_data' => $mergedFormData,
            'status' => $nextStatus,
            'current_assignees' => [],
            'due_at' => $nextStatus === WorkflowInstanceStatus::Running ? $this->slaService->computeDueAt($nextStep, now()) : null,
            'completed_at' => $nextStatus === WorkflowInstanceStatus::Completed ? now() : null,
            'rejected_at' => $nextStatus === WorkflowInstanceStatus::Rejected ? now() : null,
            'cancelled_at' => $nextStatus === WorkflowInstanceStatus::Cancelled ? now() : null,
        ])->save();

        $this->auditLogger->log($instance, WorkflowLogType::Advanced->value, [
            'step_id' => $currentStepId,
            'transition_id' => $transition->getKey(),
            'actor_id' => $actor->getKey(),
            'action_taken' => $actionName,
            'status_before' => $statusBefore,
            'status_after' => $instance->status->value,
            'payload_before' => $payloadBefore,
            'payload_after' => $this->payloadSnapshot->capture($instance),
            'form_data_snapshot' => $validated,
            'notes' => $notes,
        ]);

        return ['advanced' => true, 'transition' => $transition];
    }

    /**
     * @return array{advanced: bool, transition: WorkflowTransition|null}
     */
    protected function advanceParallel(
        WorkflowInstance $instance,
        WorkflowStep $currentStep,
        User $actor,
        string $actionName,
        array $validated,
        array $mergedFormData,
        string $statusBefore,
        array $payloadBefore,
        ?string $notes,
        ?int $currentStepId,
    ): array {
        $instance->assignments()
            ->where('step_id', $currentStep->getKey())
            ->where('assigned_to_type', 'user')
            ->where('assigned_to_id', $actor->getKey())
            ->where('status', WorkflowAssignmentStatus::Pending)
            ->update([
                'status' => WorkflowAssignmentStatus::Completed->value,
                'outcome' => $actionName,
                'completed_at' => now(),
                'updated_at' => now(),
            ]);

        $instance->forceFill(['form_data' => $mergedFormData])->save();

        $verdict = $this->parallelCoordinator->evaluate($instance->fresh(['assignments']), $currentStep);

        if (! $verdict['reached']) {
            $this->auditLogger->log($instance, WorkflowLogType::Advanced->value, [
                'step_id' => $currentStepId,
                'transition_id' => null,
                'actor_id' => $actor->getKey(),
                'action_taken' => $actionName,
                'status_before' => $statusBefore,
                'status_after' => $instance->status->value,
                'payload_before' => $payloadBefore,
                'payload_after' => $this->payloadSnapshot->capture($instance),
                'form_data_snapshot' => $validated,
                'notes' => $notes,
                'parallel' => $verdict + ['quorum_reached' => false],
            ]);

            return ['advanced' => false, 'transition' => null];
        }

        $instance->assignments()
            ->where('step_id', $currentStep->getKey())
            ->where('status', WorkflowAssignmentStatus::Pending)
            ->update([
                'status' => WorkflowAssignmentStatus::Cancelled->value,
                'updated_at' => now(),
            ]);

        $incoming = WorkflowContextData::fromInstance($instance, $mergedFormData);
        $transition = $this->transitionResolver->resolve($instance, $currentStep, $verdict['outcome'], $incoming);

        $nextStep = $transition->toStep;
        $nextStatus = $this->statusResolver->resolve($verdict['outcome'], $nextStep);

        $instance->forceFill([
            'current_step_id' => $nextStep?->getKey(),
            'form_data' => $mergedFormData,
            'status' => $nextStatus,
            'current_assignees' => [],
            'due_at' => $nextStatus === WorkflowInstanceStatus::Running ? $this->slaService->computeDueAt($nextStep, now()) : null,
            'completed_at' => $nextStatus === WorkflowInstanceStatus::Completed ? now() : null,
            'rejected_at' => $nextStatus === WorkflowInstanceStatus::Rejected ? now() : null,
            'cancelled_at' => $nextStatus === WorkflowInstanceStatus::Cancelled ? now() : null,
        ])->save();

        $this->auditLogger->log($instance, WorkflowLogType::Advanced->value, [
            'step_id' => $currentStepId,
            'transition_id' => $transition->getKey(),
            'actor_id' => $actor->getKey(),
            'action_taken' => $verdict['outcome'],
            'status_before' => $statusBefore,
            'status_after' => $instance->status->value,
            'payload_before' => $payloadBefore,
            'payload_after' => $this->payloadSnapshot->capture($instance),
            'form_data_snapshot' => $validated,
            'notes' => $notes,
            'parallel' => $verdict + ['quorum_reached' => true],
        ]);

        return ['advanced' => true, 'transition' => $transition];
    }
}
