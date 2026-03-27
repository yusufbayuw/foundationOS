<?php

namespace Modules\Workflow\Enums;

enum WorkflowDefinitionStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';
}
