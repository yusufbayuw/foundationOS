<?php

namespace Modules\Workflow\Contracts;

use Carbon\CarbonInterface;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;

interface WorkflowSlaService
{
    public function computeDueAt(?WorkflowStep $step, CarbonInterface $from): ?CarbonInterface;

    public function scheduleCheck(WorkflowInstance $instance): void;

    public function markBreached(WorkflowInstance $instance): void;
}
