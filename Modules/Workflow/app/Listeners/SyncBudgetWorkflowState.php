<?php

namespace Modules\Workflow\Listeners;

use App\Support\TypedValue;
use Modules\Finance\Models\Budget;
use Modules\Monitoring\Services\AuditTrailRecorder;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowCancelled;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Events\WorkflowStarted;
use Modules\Workflow\Models\WorkflowInstance;

class SyncBudgetWorkflowState
{
    public function handle(WorkflowStarted|WorkflowReturned|WorkflowCancelled|WorkflowAdvanced $event): void
    {
        $instance = TypedValue::model($event->instance->fresh(['subject']));
        $subject = $instance->subject;

        if (! $subject instanceof Budget) {
            return;
        }

        match ($event::class) {
            WorkflowStarted::class => $this->updateBudget($subject, [
                'status' => 'submitted',
                'approved_by' => null,
                'approved_at' => null,
            ], TypedValue::nullableInt(data_get($event, 'actor.id')), 'finance_budget_workflow_started', 'Budget approval workflow started.'),
            WorkflowReturned::class => $this->updateBudget($subject, [
                'status' => 'revision_required',
                'description' => $this->mergeDescription($subject->description, $event->notes),
            ], TypedValue::nullableInt(data_get($event, 'actor.id')), 'finance_budget_workflow_returned', 'Budget returned for revision.'),
            WorkflowCancelled::class => $this->updateBudget($subject, [
                'status' => 'cancelled',
                'description' => $this->mergeDescription($subject->description, $event->reason),
            ], TypedValue::nullableInt(data_get($event, 'actor.id')), 'finance_budget_workflow_cancelled', 'Budget workflow cancelled.'),
            WorkflowAdvanced::class => $this->syncAdvancedState($subject, $instance, $event),
            default => null,
        };
    }

    protected function syncAdvancedState(Budget $subject, WorkflowInstance $instance, WorkflowAdvanced $event): void
    {
        if ($instance->status === WorkflowInstanceStatus::Completed) {
            $actorId = TypedValue::nullableInt($event->actor->getKey());

            $this->updateBudget($subject, [
                'status' => 'approved',
                'approved_by' => $actorId,
                'approved_at' => now(),
            ], $actorId, 'finance_budget_workflow_completed', 'Budget workflow completed.');

            return;
        }

        if ($instance->status === WorkflowInstanceStatus::Rejected) {
            $this->updateBudget($subject, [
                'status' => 'rejected',
                'description' => $this->mergeDescription(
                    $subject->description,
                    TypedValue::string(data_get($instance->logs()->latest('logged_at')->first(), 'notes')),
                ),
            ], TypedValue::nullableInt($event->actor->getKey()), 'finance_budget_workflow_rejected', 'Budget workflow rejected.');

            return;
        }

        $this->updateBudget($subject, [
            'status' => 'in_review',
        ], TypedValue::nullableInt($event->actor->getKey()), 'finance_budget_workflow_in_review', 'Budget workflow is in review.');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function updateBudget(Budget $budget, array $attributes, ?int $actorId, string $action, string $description): void
    {
        $budget->forceFill($attributes)->save();

        AuditTrailRecorder::record(
            $budget,
            $action,
            null,
            $attributes,
            $description,
        );
    }

    protected function mergeDescription(?string $current, ?string $next): string
    {
        return trim(implode("\n\n", array_filter([
            TypedValue::string($current ?? ''),
            TypedValue::string($next ?? ''),
        ])));
    }
}
