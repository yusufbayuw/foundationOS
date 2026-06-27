<?php

namespace Modules\Workflow\Contracts;

use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowInstanceLog;

interface WorkflowAuditLogger
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function log(WorkflowInstance $instance, string $logType, array $context = []): WorkflowInstanceLog;
}
