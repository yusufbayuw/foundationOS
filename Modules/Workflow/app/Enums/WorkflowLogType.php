<?php

namespace Modules\Workflow\Enums;

enum WorkflowLogType: string
{
    case Started = 'started';
    case Advanced = 'advanced';
    case Returned = 'returned';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Reassigned = 'reassigned';
    case SlaScheduled = 'sla_scheduled';
    case SlaBreached = 'sla_breached';
    case SystemNote = 'system_note';
}
