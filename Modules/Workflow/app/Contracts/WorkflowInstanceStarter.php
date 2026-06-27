<?php

namespace Modules\Workflow\Contracts;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\User;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowInstance;

interface WorkflowInstanceStarter
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function start(Workflow $workflow, User $requester, array $context = [], ?Model $subject = null, ?User $startedBy = null): WorkflowInstance;
}
