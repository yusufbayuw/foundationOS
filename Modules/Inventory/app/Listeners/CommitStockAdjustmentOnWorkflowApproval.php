<?php

namespace Modules\Inventory\Listeners;

use Modules\Inventory\Models\StockAdjustment;
use Modules\Inventory\Services\StockAdjustmentService;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Events\WorkflowAdvanced;

class CommitStockAdjustmentOnWorkflowApproval
{
    public function __construct(
        private readonly StockAdjustmentService $adjustmentService,
    ) {}

    public function handle(WorkflowAdvanced $event): void
    {
        $instance = $event->instance->fresh(['subject']);

        if (! $instance->subject instanceof StockAdjustment) {
            return;
        }

        if ($instance->status !== WorkflowInstanceStatus::Completed) {
            return;
        }

        /** @var StockAdjustment $adjustment */
        $adjustment = $instance->subject;

        // The service serializes the adjustment and rechecks committed status
        // under a row lock. Do not regress a committed record to approved here.
        $this->adjustmentService->commit($adjustment);
    }
}
