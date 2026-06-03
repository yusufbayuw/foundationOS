<?php

namespace Modules\Workflow\Services;

use Carbon\CarbonInterface;
use Modules\Core\Models\User;
use Modules\Workflow\Enums\WorkflowAssignmentStatus;
use Modules\Workflow\Models\WorkflowAssignment;
use Modules\Workflow\Models\WorkflowDelegation;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Notifications\InternalWorkflowNotification;

class WorkflowEscalationService
{
    public function createAssignmentWithDelegation(WorkflowInstance $instance, WorkflowStep $step, User $assignee): WorkflowAssignment
    {
        $delegation = WorkflowDelegation::withoutTenantScope()
            ->where('tenant_id', $instance->tenant_id)
            ->where('from_user_id', $assignee->id)
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->get()
            ->first(function (WorkflowDelegation $delegation) use ($instance): bool {
                $codes = $delegation->workflow_type_codes ?? [];

                return $codes === [] || in_array($instance->workflow?->code, $codes, true);
            });

        $assignedUser = $delegation?->toUser ?? $assignee;
        $meta = [];

        if ($delegation !== null) {
            $meta = [
                'delegated_reason' => 'delegation',
                'delegated_from_user_id' => $assignee->id,
                'workflow_delegation_id' => $delegation->id,
            ];
        }

        return WorkflowAssignment::query()->create([
            'workflow_instance_id' => $instance->id,
            'step_id' => $step->id,
            'assigned_to_type' => 'user',
            'assigned_to_id' => $assignedUser->id,
            'assignment_role' => 'approver',
            'status' => WorkflowAssignmentStatus::Pending,
            'assigned_at' => now(),
            'due_at' => $step->sla_hours ? now()->addHours($step->sla_hours) : null,
            'meta' => $meta,
        ]);
    }

    public function escalateOverdue(CarbonInterface $now): int
    {
        $count = 0;

        WorkflowAssignment::query()
            ->where('status', WorkflowAssignmentStatus::Pending)
            ->whereNotNull('due_at')
            ->where('due_at', '<', $now)
            ->with(['instance'])
            ->each(function (WorkflowAssignment $assignment) use (&$count, $now): void {
                $meta = $assignment->meta ?? [];

                if (($meta['sla_state'] ?? null) === 'overdue') {
                    return;
                }

                $meta['sla_state'] = 'overdue';
                $meta['escalated_at'] = now()->toIso8601String();

                $assignment->update(['meta' => $meta]);

                $instance = $assignment->instance;

                if ($instance === null) {
                    $count++;

                    return;
                }

                $assignee = User::query()->find($assignment->assigned_to_id);

                if ($assignee !== null && config('workflow.escalation.notify_assignee', true)) {
                    $assignee->notify(new InternalWorkflowNotification(
                        $instance,
                        'Workflow task overdue',
                        sprintf(
                            'Task for %s is past its due date (due %s).',
                            $instance->subject_label ?: 'workflow instance #'.$instance->getKey(),
                            $assignment->due_at?->toDateTimeString() ?? $now->toDateTimeString(),
                        ),
                    ));
                    $meta['escalation_notified_at'] = now()->toIso8601String();
                    $assignment->update(['meta' => $meta]);
                }

                $count++;
            });

        return $count;
    }
}
