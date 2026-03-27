<?php

namespace Modules\Workflow\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowAssignmentCreated;
use Modules\Workflow\Events\WorkflowCancelled;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Events\WorkflowSlaBreached;
use Modules\Workflow\Events\WorkflowStarted;
use Modules\Workflow\Listeners\CreateAssignmentsForCurrentStep;
use Modules\Workflow\Listeners\NotifyWorkflowAssignees;
use Modules\Workflow\Listeners\RecordWorkflowMonitoringAudit;
use Modules\Workflow\Listeners\RunWorkflowAutomatedActions;
use Modules\Workflow\Listeners\ScheduleWorkflowSlaCheck;
use Modules\Workflow\Listeners\SyncWorkflowSubjectState;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        WorkflowStarted::class => [
            CreateAssignmentsForCurrentStep::class,
            ScheduleWorkflowSlaCheck::class,
            RunWorkflowAutomatedActions::class,
            RecordWorkflowMonitoringAudit::class,
            SyncWorkflowSubjectState::class,
        ],
        WorkflowAdvanced::class => [
            CreateAssignmentsForCurrentStep::class,
            ScheduleWorkflowSlaCheck::class,
            RunWorkflowAutomatedActions::class,
            RecordWorkflowMonitoringAudit::class,
            SyncWorkflowSubjectState::class,
        ],
        WorkflowCancelled::class => [
            RunWorkflowAutomatedActions::class,
            RecordWorkflowMonitoringAudit::class,
            SyncWorkflowSubjectState::class,
        ],
        WorkflowReturned::class => [
            CreateAssignmentsForCurrentStep::class,
            ScheduleWorkflowSlaCheck::class,
            RunWorkflowAutomatedActions::class,
            RecordWorkflowMonitoringAudit::class,
            SyncWorkflowSubjectState::class,
        ],
        WorkflowAssignmentCreated::class => [
            NotifyWorkflowAssignees::class,
        ],
        WorkflowSlaBreached::class => [
            RunWorkflowAutomatedActions::class,
            RecordWorkflowMonitoringAudit::class,
        ],
    ];

    protected static $shouldDiscoverEvents = true;

    protected function configureEmailVerification(): void {}
}
