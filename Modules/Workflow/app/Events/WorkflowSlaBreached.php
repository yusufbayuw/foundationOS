<?php

namespace Modules\Workflow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Workflow\Models\WorkflowInstance;

class WorkflowSlaBreached
{
    use Dispatchable, SerializesModels;

    public function __construct(public WorkflowInstance $instance) {}
}
