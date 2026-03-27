<?php

namespace Modules\Workflow\Enums;

enum WorkflowAssignmentStatus: string
{
    case Pending = 'pending';
    case Claimed = 'claimed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
}
