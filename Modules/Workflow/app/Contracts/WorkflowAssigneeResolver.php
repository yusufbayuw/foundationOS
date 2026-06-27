<?php

namespace Modules\Workflow\Contracts;

use Illuminate\Support\Collection;
use Modules\Core\Models\User;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;

interface WorkflowAssigneeResolver
{
    /**
     * @return Collection<int, User>
     */
    public function resolveUsers(WorkflowInstance $instance, WorkflowStep $step): Collection;
}
