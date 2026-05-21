<?php

namespace Modules\Workflow\Enums;

enum WorkflowGatewayType: string
{
    case None = 'none';
    case ParallelSplit = 'parallel_split';
    case ParallelJoin = 'parallel_join';
    case Inclusive = 'inclusive';
    case Exclusive = 'exclusive';
}
