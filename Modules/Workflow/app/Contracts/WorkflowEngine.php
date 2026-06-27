<?php

namespace Modules\Workflow\Contracts;

use Modules\Core\Models\User;
use Modules\Workflow\Models\WorkflowAssignment;
use Modules\Workflow\Models\WorkflowInstance;

interface WorkflowEngine
{
    /**
     * @param  array<string, mixed>  $formData
     */
    public function advance(WorkflowInstance $instance, string $actionName, array $formData, User $actor, ?string $notes = null): WorkflowInstance;

    /**
     * @param  array<string, mixed>  $formData
     */
    public function returnToStep(WorkflowInstance $instance, int $targetStepId, array $formData, User $actor, ?string $notes = null): WorkflowInstance;

    public function cancel(WorkflowInstance $instance, User $actor, ?string $reason = null): WorkflowInstance;

    public function reassign(WorkflowAssignment $assignment, User $actor, User $targetUser, ?string $reason = null): WorkflowAssignment;
}
