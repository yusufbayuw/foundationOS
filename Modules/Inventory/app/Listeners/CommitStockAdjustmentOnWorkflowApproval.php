<?php

namespace Modules\Inventory\Listeners;

use App\Support\TypedValue;
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
        $instance = TypedValue::model($event->instance->fresh(['subject']));

        if (! $instance->subject instanceof StockAdjustment) {
            return;
        }

        if ($instance->status !== WorkflowInstanceStatus::Completed) {
            return;
        }

        /** @var StockAdjustment $adjustment */
        $adjustment = $instance->subject;

        if ($adjustment->status === 'committed') {
            return;
        }

        $adjustment->forceFill(['status' => 'approved'])->save();
        $this->adjustmentService->commit($adjustment);
    }
}
