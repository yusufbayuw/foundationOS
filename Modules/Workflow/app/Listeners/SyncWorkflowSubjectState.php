<?php

namespace Modules\Workflow\Listeners;

use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowCancelled;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Events\WorkflowStarted;

class SyncWorkflowSubjectState
{
    public function handle(object $event): void
    {
        $instance = $event->instance->fresh(['subject']);
        $subject = $instance->subject;

        if (! $subject instanceof PurchaseRequisition) {
            return;
        }

        match (true) {
            $event instanceof WorkflowStarted => $subject->forceFill([
                'status' => 'submitted',
            ])->save(),
            $event instanceof WorkflowReturned => $subject->forceFill([
                'status' => 'revision_required',
                'notes' => trim(implode("\n\n", array_filter([$subject->notes, $event->notes]))),
            ])->save(),
            $event instanceof WorkflowCancelled => $subject->forceFill([
                'status' => 'cancelled',
                'notes' => trim(implode("\n\n", array_filter([$subject->notes, $event->reason]))),
            ])->save(),
            $event instanceof WorkflowAdvanced => $this->syncAdvancedState($subject, $instance, $event),
            default => null,
        };
    }

    protected function syncAdvancedState(PurchaseRequisition $subject, $instance, WorkflowAdvanced $event): void
    {
        if ($instance->status === WorkflowInstanceStatus::Completed) {
            $subject->forceFill([
                'status' => 'approved',
                'approved_by' => $event->actor->getKey(),
                'approved_at' => now(),
            ])->save();

            return;
        }

        if ($instance->status === WorkflowInstanceStatus::Rejected) {
            $subject->forceFill([
                'status' => 'rejected',
                'rejection_reason' => data_get($instance->logs()->latest('logged_at')->first(), 'notes'),
            ])->save();

            return;
        }

        $subject->forceFill([
            'status' => 'in_review',
        ])->save();
    }
}
