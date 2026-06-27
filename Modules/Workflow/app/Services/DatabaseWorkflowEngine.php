<?php

namespace Modules\Workflow\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\User;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowCancelled;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Models\WorkflowAssignment;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Services\Engine\Handlers\AdvanceWorkflowHandler;
use Modules\Workflow\Services\Engine\Handlers\CancelWorkflowHandler;
use Modules\Workflow\Services\Engine\Handlers\ReassignWorkflowHandler;
use Modules\Workflow\Services\Engine\Handlers\ReturnWorkflowHandler;
use Modules\Workflow\Services\Engine\Support\WorkflowInstanceLocker;

class DatabaseWorkflowEngine implements WorkflowEngine
{
    public function __construct(
        private readonly WorkflowInstanceLocker $instanceLocker,
        private readonly AdvanceWorkflowHandler $advanceHandler,
        private readonly ReturnWorkflowHandler $returnHandler,
        private readonly CancelWorkflowHandler $cancelHandler,
        private readonly ReassignWorkflowHandler $reassignHandler,
        private readonly WorkflowSnapshotStepResolver $snapshotStepResolver,
    ) {}

    public function advance(WorkflowInstance $instance, string $actionName, array $formData, User $actor, ?string $notes = null): WorkflowInstance
    {
        $result = DB::transaction(function () use ($instance, $actionName, $formData, $actor, $notes): array {
            $locked = $this->instanceLocker->lock($instance, ['currentStep', 'assignments', 'workflow']);

            return $this->advanceHandler->handle($locked, $actionName, $formData, $actor, $notes);
        });

        $fresh = $instance->fresh(['currentStep', 'assignments', 'logs']);

        if ($result['advanced'] && $result['transition'] !== null) {
            WorkflowAdvanced::dispatch($fresh, $result['transition'], $actor);
        }

        return $fresh;
    }

    public function returnToStep(WorkflowInstance $instance, int $targetStepId, array $formData, User $actor, ?string $notes = null): WorkflowInstance
    {
        $instance = DB::transaction(function () use ($instance, $targetStepId, $formData, $actor, $notes) {
            $locked = $this->instanceLocker->lock($instance, ['workflow.steps', 'assignments', 'currentStep']);

            return $this->returnHandler->handle($locked, $targetStepId, $formData, $actor, $notes);
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
            $locked = $this->instanceLocker->lock($instance, ['assignments', 'currentStep']);

            return $this->cancelHandler->handle($locked, $actor, $reason);
        });

        WorkflowCancelled::dispatch($instance, $actor, $reason);

        return $instance;
    }

    public function reassign(WorkflowAssignment $assignment, User $actor, User $targetUser, ?string $reason = null): WorkflowAssignment
    {
        return DB::transaction(function () use ($assignment, $actor, $targetUser, $reason) {
            return $this->reassignHandler->handle($assignment, $actor, $targetUser, $reason);
        });
    }
}
