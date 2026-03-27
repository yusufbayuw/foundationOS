<?php

namespace Modules\Workflow\Enums;

enum WorkflowInstanceStatus: string
{
    case Running = 'running';
    case Completed = 'completed';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
}
