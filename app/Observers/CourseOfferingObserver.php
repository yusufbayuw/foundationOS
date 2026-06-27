<?php

namespace App\Observers;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Integrations\Moodle\MoodleSyncContext;
use Illuminate\Support\Carbon;
use Modules\Campus\Models\CourseOffering;

class CourseOfferingObserver
{
    public function __construct(protected MoodleOutboxService $outbox) {}

    public function created(CourseOffering $offering): void
    {
        $this->enqueueByState($offering);
    }

    public function updated(CourseOffering $offering): void
    {
        $relevant = [
            'tenant_id',
            'organization_id',
            'course_id',
            'academic_period_id',
            'class_code',
            'status',
            'capacity',
            'deleted_at',
        ];

        if (! $offering->wasChanged($relevant)) {
            return;
        }

        $this->enqueueByState($offering);
    }

    public function deleted(CourseOffering $offering): void
    {
        $this->enqueue($offering, MoodleOutboxService::ACTION_DEACTIVATE);
    }

    public function restored(CourseOffering $offering): void
    {
        $this->enqueueByState($offering);
    }

    public function forceDeleted(CourseOffering $offering): void
    {
        $this->enqueue($offering, MoodleOutboxService::ACTION_DEACTIVATE);
    }

    protected function enqueueByState(CourseOffering $offering): void
    {
        $action = $this->shouldDeactivate($offering)
            ? MoodleOutboxService::ACTION_DEACTIVATE
            : MoodleOutboxService::ACTION_UPSERT;

        $this->enqueue($offering, $action);
    }

    protected function enqueue(CourseOffering $offering, string $action): void
    {
        if (MoodleSyncContext::disabled()) {
            return;
        }

        $version = optional($offering->updated_at)->timestamp ?? now()->timestamp;
        $dedupe = "course_offering:{$offering->id}:{$action}:{$version}";

        $this->outbox->enqueue(
            MoodleOutboxService::ENTITY_COURSE_OFFERING,
            (int) $offering->id,
            (int) $offering->tenant_id,
            $action,
            [
                'tenant_id' => (int) $offering->tenant_id,
                'course_id' => (int) $offering->course_id,
                'academic_period_id' => (int) $offering->academic_period_id,
                'class_code' => $offering->class_code,
                'status' => $offering->status,
                'deleted_at' => (($d = $offering->getAttribute('deleted_at')) instanceof Carbon ? $d->toDateTimeString() : null),
            ],
            $dedupe,
        );
    }

    protected function shouldDeactivate(CourseOffering $offering): bool
    {
        if ($offering->trashed()) {
            return true;
        }

        $status = strtolower((string) $offering->status);

        return in_array($status, ['cancelled', 'archived', 'closed'], true);
    }
}
