<?php

namespace Modules\Workflow\Contracts;

use Illuminate\Support\Collection;
use Modules\Core\Models\User;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;

interface WorkflowDynamicAssigneeResolver
{
    /**
     * @return Collection<int, User>
     */
    public function resolve(WorkflowInstance $instance, WorkflowStep $step): Collection;
}
