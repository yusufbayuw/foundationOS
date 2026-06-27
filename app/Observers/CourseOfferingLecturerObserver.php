<?php

namespace App\Observers;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Integrations\Moodle\MoodleSyncContext;
use App\Support\TypedValue;
use Modules\Campus\Models\CourseOfferingLecturer;

class CourseOfferingLecturerObserver
{
    public function __construct(protected MoodleOutboxService $outbox) {}

    public function created(CourseOfferingLecturer $assignment): void
    {
        $this->enqueueByState($assignment);
    }

    public function updated(CourseOfferingLecturer $assignment): void
    {
        $relevant = ['tenant_id', 'course_offering_id', 'lecturer_id', 'role', 'is_active', 'removed_at'];

        if (! $assignment->wasChanged($relevant)) {
            return;
        }

        $this->enqueueByState($assignment);
    }

    public function deleted(CourseOfferingLecturer $assignment): void
    {
        $this->enqueue($assignment, MoodleOutboxService::ACTION_UNASSIGN);
    }

    protected function enqueueByState(CourseOfferingLecturer $assignment): void
    {
        $action = $this->shouldUnassign($assignment)
            ? MoodleOutboxService::ACTION_UNASSIGN
            : MoodleOutboxService::ACTION_ASSIGN;

        $this->enqueue($assignment, $action);
    }

    protected function enqueue(CourseOfferingLecturer $assignment, string $action): void
    {
        if (MoodleSyncContext::disabled()) {
            return;
        }

        $version = TypedValue::int(data_get($assignment, 'updated_at.timestamp'), TypedValue::int(now()->timestamp));
        $dedupe = "lecturer_assignment:{$assignment->id}:{$action}:{$version}";

        $this->outbox->enqueue(
            MoodleOutboxService::ENTITY_LECTURER_ASSIGNMENT,
            (int) $assignment->id,
            (int) $assignment->tenant_id,
            $action,
            [
                'tenant_id' => (int) $assignment->tenant_id,
                'course_offering_id' => (int) $assignment->course_offering_id,
                'lecturer_id' => (int) $assignment->lecturer_id,
                'role' => $assignment->role->value,
                'is_active' => (bool) $assignment->is_active,
                'removed_at' => $assignment->removed_at?->toDateTimeString(),
            ],
            $dedupe,
        );
    }

    protected function shouldUnassign(CourseOfferingLecturer $assignment): bool
    {
        return ! $assignment->is_active || $assignment->removed_at !== null;
    }
}
