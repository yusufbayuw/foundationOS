<?php

namespace Modules\Workflow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Models\User;
use Modules\Workflow\Models\WorkflowInstance;

class WorkflowStarted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WorkflowInstance $instance,
        public User $actor,
    ) {}
}
