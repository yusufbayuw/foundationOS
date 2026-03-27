<?php

namespace Modules\Workflow\Enums;

enum WorkflowAutomationTrigger: string
{
    case Started = 'started';
    case Advanced = 'advanced';
    case Completed = 'completed';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case SlaBreached = 'sla_breached';
    case Returned = 'returned';
}
