<?php

namespace Modules\Workflow\Contracts;

use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;

interface WorkflowTransitionResolver
{
    /**
     * @param  array<string, mixed>  $incomingData
     */
    public function resolve(WorkflowInstance $instance, WorkflowStep $step, string $actionName, array $incomingData): WorkflowTransition;
}
