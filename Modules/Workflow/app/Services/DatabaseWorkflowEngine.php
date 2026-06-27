<?php

namespace Modules\Workflow\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\User;
use Modules\Workflow\Contracts\WorkflowAuditLogger;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Contracts\WorkflowFormSchemaValidator;
use Modules\Workflow\Contracts\WorkflowSlaService;
use Modules\Workflow\Contracts\WorkflowTransitionResolver;
use Modules\Workflow\Enums\WorkflowAssignmentStatus;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Enums\WorkflowLogType;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowCancelled;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Exceptions\WorkflowAuthorizationException;
use Modules\Workflow\Exceptions\WorkflowEvidenceRequiredException;
use Modules\Workflow\Models\WorkflowAssignment;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Modules\Workflow\Support\WorkflowActionGuard;
use Modules\Workflow\Support\WorkflowContextData;

class DatabaseWorkflowEngine implements WorkflowEngine
{
    public function __construct(
        private readonly WorkflowFormSchemaValidator $validator,
        private readonly WorkflowTransitionResolver $transitionResolver,
        private readonly WorkflowAuditLogger $auditLogger,
        private readonly WorkflowSlaService $slaService,
        private readonly WorkflowParallelCoordinator $parallelCoordinator,
        private readonly WorkflowSnapshotStepResolver $snapshotStepResolver,
        private readonly WorkflowActionGuard $actionGuard,
    ) {}

    public function advance(WorkflowInstance $instance, string $actionName, array $formData, User $actor, ?string $notes = null): WorkflowInstance
    {
        $result = DB::transaction(function () use ($instance, $actionName, $formData, $actor, $notes): array {
            // Row-lock the instance to serialize concurrent advance() calls
            // from parallel approvers.
            $instance = WorkflowInstance::query()
                ->whereKey($instance->getKey())
                ->lockForUpdate()
                ->first()
                ->load(['currentStep', 'assignments', 'workflow']);

            $this->authorizeActor($instance, $actor);

            $currentStep = $this->snapshotStepResolver->resolveCurrent($instance);
            $this->actionGuard->assertAdvanceActionAllowed($instance, $currentStep, $actionName);

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
            $payloadBefore = $this->payloadSnapshot($instance);
            $mergedFormData = array_replace_recursive($instance->form_data ?? [], $validated);

            if ($currentStep && $this->parallelCoordinator->isParallel($currentStep)) {
                return $this->advanceParallel(
                    $instance, $currentStep, $actor, $actionName, $validated,
                    $mergedFormData, $statusBefore, $payloadBefore, $notes, $currentStepId,
                );
            }

            return $this->advanceLinear(
                $instance, $currentStep, $actor, $actionName, $validated,
                $mergedFormData, $statusBefore, $payloadBefore, $notes, $currentStepId,
            );
        });

        $fresh = $instance->fresh(['currentStep', 'assignments', 'logs']);

        if ($result['advanced'] && $result['transition'] !== null) {
            WorkflowAdvanced::dispatch($fresh, $result['transition'], $actor);
        }

        return $fresh;
    }

    /**
     * @return array{advanced:bool,transition: WorkflowTransition|null}
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
        $nextStatus = $this->determineStatus($actionName, $nextStep);

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
            'payload_after' => $this->payloadSnapshot($instance),
            'form_data_snapshot' => $validated,
            'notes' => $notes,
        ]);

        return ['advanced' => true, 'transition' => $transition];
    }

    /**
     * Parallel/quorum step: record actor's outcome, then evaluate quorum.
     * Only advance once the coordinator says the step has reached a decision.
     *
     * @return array{advanced:bool,transition: WorkflowTransition|null}
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
        // Record only this actor's vote.
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

        // Persist merged form data even when still waiting on other approvers.
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
                'payload_after' => $this->payloadSnapshot($instance),
                'form_data_snapshot' => $validated,
                'notes' => $notes,
                'parallel' => $verdict + ['quorum_reached' => false],
            ]);

            return ['advanced' => false, 'transition' => null];
        }

        // Quorum reached: cancel any still-pending assignments at this step.
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
        $nextStatus = $this->determineStatus($verdict['outcome'], $nextStep);

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
            'payload_after' => $this->payloadSnapshot($instance),
            'form_data_snapshot' => $validated,
            'notes' => $notes,
            'parallel' => $verdict + ['quorum_reached' => true],
        ]);

        return ['advanced' => true, 'transition' => $transition];
    }

    public function returnToStep(WorkflowInstance $instance, int $targetStepId, array $formData, User $actor, ?string $notes = null): WorkflowInstance
    {
        $instance = DB::transaction(function () use ($instance, $targetStepId, $formData, $actor, $notes) {
            $instance = WorkflowInstance::query()
                ->whereKey($instance->getKey())
                ->lockForUpdate()
                ->first()
                ->load(['workflow.steps', 'assignments', 'currentStep']);

            $this->authorizeActor($instance, $actor);
            $statusBefore = $instance->status->value;

            $targetStep = $this->snapshotStepResolver->materialize($instance, $targetStepId);

            if (! $targetStep) {
                throw new WorkflowAuthorizationException('Target return step is not part of the workflow.');
            }

            $currentStep = $this->snapshotStepResolver->resolveCurrent($instance);
            $incomingContext = WorkflowContextData::fromInstance($instance, $formData);
            $validated = $this->validator->validate($currentStep, $formData, $incomingContext);
            $payloadBefore = $this->payloadSnapshot($instance);

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
                'payload_after' => $this->payloadSnapshot($instance),
                'form_data_snapshot' => $validated,
                'notes' => $notes,
            ]);

            return $instance->fresh(['currentStep', 'assignments', 'logs']);
        });

        WorkflowReturned::dispatch(
            $instance,
            $this->snapshotStepResolver->resolveCurrent($instance),
            $actor,
            $notes,
        );

        return $instance;
    }

    public function cancel(WorkflowInstance $instance, User $actor, ?string $reason = null): WorkflowInstance
    {
        $instance = DB::transaction(function () use ($instance, $actor, $reason) {
            $instance = WorkflowInstance::query()
                ->whereKey($instance->getKey())
                ->lockForUpdate()
                ->first()
                ->load(['assignments', 'currentStep']);

            $this->authorizeActor($instance, $actor);
            $statusBefore = $instance->status->value;
            $payloadBefore = $this->payloadSnapshot($instance);

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
                'payload_after' => $this->payloadSnapshot($instance),
                'notes' => $reason,
            ]);

            return $instance->fresh(['currentStep', 'assignments', 'logs']);
        });

        WorkflowCancelled::dispatch($instance, $actor, $reason);

        return $instance;
    }

    public function reassign(WorkflowAssignment $assignment, User $actor, User $targetUser, ?string $reason = null): WorkflowAssignment
    {
        return DB::transaction(function () use ($assignment, $actor, $targetUser, $reason) {
            $assignment = $assignment->fresh(['instance', 'step']);

            $instance = WorkflowInstance::query()
                ->whereKey($assignment->workflow_instance_id)
                ->lockForUpdate()
                ->first();

            if ($instance) {
                $assignment->setRelation('instance', $instance);
            }

            $this->authorizeActor($assignment->instance, $actor);

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
        });
    }

    protected function authorizeActor(WorkflowInstance $instance, User $actor): void
    {
        if ($actor->isGlobalSuperAdmin()) {
            return;
        }

        $authorized = $instance->assignments()
            ->where('status', WorkflowAssignmentStatus::Pending)
            ->where('assigned_to_type', 'user')
            ->where('assigned_to_id', $actor->getKey())
            ->exists();

        if (! $authorized) {
            throw new WorkflowAuthorizationException('The actor does not have an active assignment for this workflow instance.');
        }
    }

    protected function determineStatus(string $actionName, ?WorkflowStep $nextStep): WorkflowInstanceStatus
    {
        if ($actionName === 'reject') {
            return WorkflowInstanceStatus::Rejected;
        }

        if ($actionName === 'cancel') {
            return WorkflowInstanceStatus::Cancelled;
        }

        if (! $nextStep || $nextStep->is_terminal) {
            return WorkflowInstanceStatus::Completed;
        }

        return WorkflowInstanceStatus::Running;
    }

    protected function payloadSnapshot(WorkflowInstance $instance): array
    {
        return [
            'context_data' => $instance->context_data,
            'form_data' => $instance->form_data,
            'computed_data' => $instance->computed_data,
            'current_assignees' => $instance->current_assignees,
            'status' => $instance->status?->value ?? $instance->status,
            'current_step_id' => $instance->current_step_id,
        ];
    }
}
