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
    ) {}

    /**
     * @param  array<string, mixed>  $formData
     */
    public function advance(WorkflowInstance $instance, string $actionName, array $formData, User $actor, ?string $notes = null): WorkflowInstance
    {
        $result = DB::transaction(function () use ($instance, $actionName, $formData, $actor, $notes): array {
            $instance = $this->lockInstance($instance, ['currentStep', 'assignments', 'workflow']);

            $this->authorizeActor($instance, $actor);

            $currentStep = $this->snapshotStepResolver->resolveCurrent($instance);

            if ($currentStep instanceof WorkflowStep && $currentStep->requiresEvidence()) {
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

            if (! $currentStep instanceof WorkflowStep) {
                throw new \RuntimeException('Workflow instance does not have a resolvable current step.');
            }

            $currentStepId = $instance->current_step_id;
            $statusBefore = $instance->status->value;
            $incomingContext = WorkflowContextData::fromInstance($instance, $formData);
            /** @var array<string, mixed> $validated */
            $validated = $this->validator->validate($currentStep, $formData, $incomingContext);
            $payloadBefore = $this->payloadSnapshot($instance);
            /** @var array<string, mixed> $mergedFormData */
            $mergedFormData = array_replace_recursive($instance->form_data ?? [], $validated);

            if ($this->parallelCoordinator->isParallel($currentStep)) {
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

        if ($fresh === null) {
            throw new \RuntimeException('Workflow instance no longer exists.');
        }

        if ($result['advanced'] && $result['transition'] !== null) {
            WorkflowAdvanced::dispatch($fresh, $result['transition'], $actor);
        }

        return $fresh;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<string, mixed>  $mergedFormData
     * @param  array<string, mixed>  $payloadBefore
     * @return array{advanced: bool, transition: WorkflowTransition|null}
     */
    protected function advanceLinear(
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
        $incoming = WorkflowContextData::fromInstance($instance, $mergedFormData);
        $transition = $this->transitionResolver->resolve($instance, $currentStep, $actionName, $incoming);

        $nextStep = $transition->toStep instanceof WorkflowStep ? $transition->toStep : null;
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
     * @param  array<string, mixed>  $validated
     * @param  array<string, mixed>  $mergedFormData
     * @param  array<string, mixed>  $payloadBefore
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

        $refreshed = $instance->fresh(['assignments']);

        if ($refreshed === null) {
            throw new \RuntimeException('Workflow instance no longer exists.');
        }

        $verdict = $this->parallelCoordinator->evaluate($refreshed, $currentStep);

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

        $nextStep = $transition->toStep instanceof WorkflowStep ? $transition->toStep : null;
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

    /**
     * @param  array<string, mixed>  $formData
     */
    public function returnToStep(WorkflowInstance $instance, int $targetStepId, array $formData, User $actor, ?string $notes = null): WorkflowInstance
    {
        $targetStep = null;

        $returned = DB::transaction(function () use ($instance, $targetStepId, $formData, $actor, $notes, &$targetStep): WorkflowInstance {
            $instance = $this->lockInstance($instance, ['workflow.steps', 'assignments', 'currentStep']);

            $this->authorizeActor($instance, $actor);
            $statusBefore = $instance->status->value;

            $targetStep = $this->snapshotStepResolver->materialize($instance, $targetStepId);

            if (! $targetStep instanceof WorkflowStep) {
                throw new WorkflowAuthorizationException('Target return step is not part of the workflow.');
            }

            $currentStep = $this->snapshotStepResolver->resolveCurrent($instance);

            if (! $currentStep instanceof WorkflowStep) {
                throw new \RuntimeException('Workflow instance does not have a resolvable current step.');
            }

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

            $fresh = $instance->fresh(['currentStep', 'assignments', 'logs']);

            if ($fresh === null) {
                throw new \RuntimeException('Workflow instance no longer exists.');
            }

            return $fresh;
        });

        $returnStep = $this->snapshotStepResolver->resolveCurrent($returned)
            ?? ($targetStep instanceof WorkflowStep ? $targetStep : null);

        if (! $returnStep instanceof WorkflowStep) {
            throw new \RuntimeException('Workflow return step could not be resolved.');
        }

        WorkflowReturned::dispatch(
            $returned,
            $returnStep,
            $actor,
            $notes,
        );

        return $returned;
    }

    public function cancel(WorkflowInstance $instance, User $actor, ?string $reason = null): WorkflowInstance
    {
        $cancelled = DB::transaction(function () use ($instance, $actor, $reason): WorkflowInstance {
            $instance = $this->lockInstance($instance, ['assignments', 'currentStep']);

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

            $fresh = $instance->fresh(['currentStep', 'assignments', 'logs']);

            if ($fresh === null) {
                throw new \RuntimeException('Workflow instance no longer exists.');
            }

            return $fresh;
        });

        WorkflowCancelled::dispatch($cancelled, $actor, $reason);

        return $cancelled;
    }

    public function reassign(WorkflowAssignment $assignment, User $actor, User $targetUser, ?string $reason = null): WorkflowAssignment
    {
        return DB::transaction(function () use ($assignment, $actor, $targetUser, $reason): WorkflowAssignment {
            $assignment = $assignment->fresh(['instance', 'step']);

            if ($assignment === null) {
                throw new \RuntimeException('Workflow assignment no longer exists.');
            }

            $instance = WorkflowInstance::query()
                ->whereKey($assignment->workflow_instance_id)
                ->lockForUpdate()
                ->first();

            if (! $instance instanceof WorkflowInstance) {
                throw new \RuntimeException('Workflow instance no longer exists.');
            }

            $assignment->setRelation('instance', $instance);

            $this->authorizeActor($instance, $actor);

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

            $instance->forceFill([
                'current_assignees' => [[
                    'id' => $targetUser->getKey(),
                    'name' => $targetUser->name,
                    'email' => $targetUser->email,
                ]],
            ])->save();

            $this->auditLogger->log($instance->fresh() ?? $instance, WorkflowLogType::Reassigned->value, [
                'step_id' => $assignment->step_id,
                'actor_id' => $actor->getKey(),
                'action_taken' => 'reassign',
                'notes' => $reason,
            ]);

            return $newAssignment;
        });
    }

    /**
     * @param  list<string>  $relations
     */
    protected function lockInstance(WorkflowInstance $instance, array $relations = []): WorkflowInstance
    {
        $locked = WorkflowInstance::query()
            ->whereKey($instance->getKey())
            ->lockForUpdate()
            ->first();

        if (! $locked instanceof WorkflowInstance) {
            throw new \RuntimeException('Workflow instance no longer exists.');
        }

        if ($relations !== []) {
            $locked->load($relations);
        }

        return $locked;
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

    /**
     * @return array<string, mixed>
     */
    protected function payloadSnapshot(WorkflowInstance $instance): array
    {
        return [
            'context_data' => $instance->context_data,
            'form_data' => $instance->form_data,
            'computed_data' => $instance->computed_data,
            'current_assignees' => $instance->current_assignees,
            'status' => $instance->status->value,
            'current_step_id' => $instance->current_step_id,
        ];
    }
}
