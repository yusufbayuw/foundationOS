<?php

namespace Modules\Workflow\Listeners;

use App\Support\TypedValue;
use Modules\Core\Models\User;
use Modules\Workflow\Events\WorkflowAssignmentCreated;
use Modules\Workflow\Notifications\InternalWorkflowNotification;
use Modules\Workflow\Services\WorkflowSnapshotStepResolver;

class NotifyWorkflowAssignees
{
    public function __construct(private readonly WorkflowSnapshotStepResolver $snapshotStepResolver) {}

    public function handle(WorkflowAssignmentCreated $event): void
    {
        $assignment = $event->assignment->loadMissing('instance');
        $instance = $assignment->instance;

        if ($instance === null) {
            return;
        }

        $assignee = User::query()->find($assignment->assigned_to_id);

        if ($assignee === null) {
            return;
        }

        $stepName = $this->snapshotStepResolver->resolveCurrent($instance)->name
            ?? 'Workflow step';

        $assignee->notify(new InternalWorkflowNotification(
            $instance,
            'Workflow task assigned',
            sprintf(
                'You have a pending task on %s (%s).',
                $instance->subject_label ?: 'workflow instance #'.TypedValue::string($instance->getKey()),
                $stepName,
            ),
        ));

        $assignment->forceFill([
            'meta' => array_merge($assignment->meta ?? [], [
                'notified_at' => now()->toDateTimeString(),
            ]),
        ])->save();
    }
}
