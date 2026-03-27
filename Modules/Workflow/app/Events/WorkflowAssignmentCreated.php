<?php

namespace Modules\Workflow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Workflow\Models\WorkflowAssignment;

class WorkflowAssignmentCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public WorkflowAssignment $assignment) {}
}
