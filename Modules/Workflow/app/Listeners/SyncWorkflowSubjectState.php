<?php

namespace Modules\Workflow\Listeners;

use App\Support\TypedValue;
use Modules\Procurement\Events\PurchaseRequisitionApproved;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowCancelled;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Events\WorkflowStarted;
use Modules\Workflow\Models\WorkflowInstance;

class SyncWorkflowSubjectState
{
    public function handle(WorkflowStarted|WorkflowReturned|WorkflowCancelled|WorkflowAdvanced $event): void
    {
        $instance = TypedValue::model($event->instance->fresh(['subject']));
        $subject = $instance->subject;

        if (! $subject instanceof PurchaseRequisition) {
            return;
        }

        match ($event::class) {
            WorkflowStarted::class => $subject->forceFill([
                'status' => 'submitted',
                'ready_for_sourcing' => false,
            ])->save(),
            WorkflowReturned::class => $subject->forceFill([
                'status' => 'revision_required',
                'ready_for_sourcing' => false,
                'notes' => trim(implode("\n\n", array_filter([$subject->notes, $event->notes]))),
            ])->save(),
            WorkflowCancelled::class => $subject->forceFill([
                'status' => 'cancelled',
                'ready_for_sourcing' => false,
                'notes' => trim(implode("\n\n", array_filter([$subject->notes, $event->reason]))),
            ])->save(),
            WorkflowAdvanced::class => $this->syncAdvancedState($subject, $instance, $event),
            default => null,
        };
    }

    protected function syncAdvancedState(PurchaseRequisition $subject, WorkflowInstance $instance, WorkflowAdvanced $event): void
    {
        if ($instance->status === WorkflowInstanceStatus::Completed) {
            $subject->forceFill([
                'status' => 'approved',
                'ready_for_sourcing' => true,
                'approved_by' => TypedValue::nullableInt($event->actor->getKey()),
                'approved_at' => now(),
            ])->save();

            $freshSubject = TypedValue::model($subject->fresh());
            PurchaseRequisitionApproved::dispatch($freshSubject, $event->actor);

            return;
        }

        if ($instance->status === WorkflowInstanceStatus::Rejected) {
            $subject->forceFill([
                'status' => 'rejected',
                'ready_for_sourcing' => false,
                'rejection_reason' => data_get($instance->logs()->latest('logged_at')->first(), 'notes'),
            ])->save();

            return;
        }

        $subject->forceFill([
            'status' => 'in_review',
            'ready_for_sourcing' => false,
        ])->save();
    }
}
