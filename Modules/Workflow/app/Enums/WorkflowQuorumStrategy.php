<?php

namespace Modules\Workflow\Enums;

enum WorkflowQuorumStrategy: string
{
    case All = 'all';
    case Majority = 'majority';
    case Count = 'count';
    case Percentage = 'percentage';
}
