<?php

namespace Modules\Workflow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Models\User;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowTransition;

class WorkflowAdvanced
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WorkflowInstance $instance,
        public WorkflowTransition $transition,
        public User $actor,
    ) {}
}
