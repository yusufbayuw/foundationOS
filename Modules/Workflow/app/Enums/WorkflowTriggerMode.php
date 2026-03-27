<?php

namespace Modules\Workflow\Enums;

enum WorkflowTriggerMode: string
{
    case Manual = 'manual';
    case ModelEvent = 'model_event';
    case Api = 'api';
}
