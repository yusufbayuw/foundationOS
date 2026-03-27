<?php

namespace Modules\Workflow\Enums;

enum WorkflowAutomationActionType: string
{
    case InternalNotification = 'internal_notification';
    case AuditNote = 'audit_note';
    case DispatchJob = 'dispatch_job';
    case SetComputedData = 'set_computed_data';
}
