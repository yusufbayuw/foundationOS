<?php

namespace Modules\Workflow\Contracts;

use Illuminate\Support\Collection;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;

interface WorkflowDynamicAssigneeResolver
{
    public function resolve(WorkflowInstance $instance, WorkflowStep $step): Collection;
}
