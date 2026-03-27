<?php

namespace Modules\Workflow\Enums;

enum WorkflowAssigneeType: string
{
    case Role = 'role';
    case User = 'user';
    case RequesterManager = 'requester_manager';
    case SubjectField = 'subject_field';
    case Resolver = 'resolver';
}
