<?php

namespace Modules\Workflow\Services\Engine\Support;

use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Models\WorkflowStep;

class StatusResolver
{
    public function resolve(string $actionName, ?WorkflowStep $nextStep): WorkflowInstanceStatus
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
}
