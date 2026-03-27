<?php

namespace Modules\Workflow\Listeners;

use Modules\Workflow\Events\WorkflowAssignmentCreated;

class NotifyWorkflowAssignees
{
    public function handle(WorkflowAssignmentCreated $event): void
    {
        // Notification channels can be wired later without changing engine behavior.
        $event->assignment->forceFill([
            'meta' => array_merge($event->assignment->meta ?? [], [
                'notified_at' => now()->toDateTimeString(),
            ]),
        ])->save();
    }
}
