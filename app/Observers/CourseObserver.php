<?php

namespace App\Observers;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Integrations\Moodle\MoodleSyncContext;
use Illuminate\Support\Carbon;
use Modules\Campus\Models\Course;

class CourseObserver
{
    public function __construct(protected MoodleOutboxService $outbox) {}

    public function created(Course $course): void
    {
        $this->enqueue($course, MoodleOutboxService::ACTION_UPSERT);
    }

    public function updated(Course $course): void
    {
        $relevant = [
            'tenant_id',
            'code',
            'name',
            'description',
            'is_active',
            'deleted_at',
        ];

        if (! $course->wasChanged($relevant)) {
            return;
        }

        $action = $this->shouldDeactivate($course)
            ? MoodleOutboxService::ACTION_DEACTIVATE
            : MoodleOutboxService::ACTION_UPSERT;

        $this->enqueue($course, $action);
    }

    public function deleted(Course $course): void
    {
        $this->enqueue($course, MoodleOutboxService::ACTION_DEACTIVATE);
    }

    public function restored(Course $course): void
    {
        $this->enqueue($course, MoodleOutboxService::ACTION_UPSERT);
    }

    public function forceDeleted(Course $course): void
    {
        $this->enqueue($course, MoodleOutboxService::ACTION_DEACTIVATE);
    }

    protected function enqueue(Course $course, string $action): void
    {
        if (MoodleSyncContext::disabled()) {
            return;
        }

        $version = optional($course->updated_at)->timestamp ?? now()->timestamp;
        $dedupe = "course:{$course->id}:{$action}:{$version}";

        $this->outbox->enqueue(
            MoodleOutboxService::ENTITY_COURSE,
            (int) $course->id,
            (int) $course->tenant_id,
            $action,
            [
                'tenant_id' => (int) $course->tenant_id,
                'code' => $course->code,
                'is_active' => (bool) $course->is_active,
                'deleted_at' => (($d = $course->getAttribute('deleted_at')) instanceof Carbon ? $d->toDateTimeString() : null),
            ],
            $dedupe,
        );
    }

    protected function shouldDeactivate(Course $course): bool
    {
        if ($course->trashed()) {
            return true;
        }

        return ! (bool) $course->is_active;
    }
}
