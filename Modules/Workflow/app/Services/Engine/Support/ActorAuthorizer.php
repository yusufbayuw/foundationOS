<?php

namespace Modules\Workflow\Services\Engine\Support;

use Modules\Core\Models\User;
use Modules\Workflow\Enums\WorkflowAssignmentStatus;
use Modules\Workflow\Exceptions\WorkflowAuthorizationException;
use Modules\Workflow\Models\WorkflowInstance;

class ActorAuthorizer
{
    public function authorize(WorkflowInstance $instance, User $actor): void
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
}
