<?php

namespace Modules\Workflow\Listeners;

use App\Support\TypedValue;
use Modules\Workflow\Contracts\WorkflowSlaService;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Events\WorkflowStarted;

class ScheduleWorkflowSlaCheck
{
    public function __construct(private readonly WorkflowSlaService $slaService) {}

    public function handle(WorkflowStarted|WorkflowAdvanced|WorkflowReturned $event): void
    {
        $instance = TypedValue::model($event->instance->fresh());
        $this->slaService->scheduleCheck($instance);
    }
}
