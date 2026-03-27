<?php

namespace Modules\Workflow\Contracts;

use Illuminate\Support\Collection;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;

interface WorkflowAssigneeResolver
{
    public function resolveUsers(WorkflowInstance $instance, WorkflowStep $step): Collection;
}
