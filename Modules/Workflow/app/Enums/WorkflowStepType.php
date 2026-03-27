<?php

namespace Modules\Workflow\Enums;

enum WorkflowStepType: string
{
    case Start = 'start';
    case Task = 'task';
    case Approval = 'approval';
    case Gateway = 'gateway';
    case End = 'end';
}
