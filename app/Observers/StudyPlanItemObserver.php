<?php

namespace App\Observers;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Integrations\Moodle\MoodleSyncContext;
use Illuminate\Support\Carbon;
use Modules\Campus\Models\StudyPlanItem;

class StudyPlanItemObserver
{
    /**
     * Item statuses that mean "student should be enrolled in the offering".
     */
    public const ENROLLED_STATUSES = ['approved', 'enrolled', 'active'];

    /**
     * Item statuses that mean "student should be unenrolled".
     */
    public const UNENROLLED_STATUSES = ['cancelled', 'dropped', 'withdrawn'];

    public function __construct(protected MoodleOutboxService $outbox) {}

    public function created(StudyPlanItem $item): void
    {
        $this->enqueueByState($item);
    }

    public function updated(StudyPlanItem $item): void
    {
        if (! $item->wasChanged(['status', 'course_offering_id', 'study_plan_id', 'tenant_id', 'deleted_at'])) {
            return;
        }

        $this->enqueueByState($item);
    }

    public function deleted(StudyPlanItem $item): void
    {
        $this->enqueue($item, MoodleOutboxService::ACTION_UNENROLL);
    }

    public function restored(StudyPlanItem $item): void
    {
        $this->enqueueByState($item);
    }

    public function forceDeleted(StudyPlanItem $item): void
    {
        $this->enqueue($item, MoodleOutboxService::ACTION_UNENROLL);
    }

    protected function enqueueByState(StudyPlanItem $item): void
    {
        if ($this->shouldUnenroll($item)) {
            $this->enqueue($item, MoodleOutboxService::ACTION_UNENROLL);

            return;
        }

        if ($this->shouldEnroll($item)) {
            $this->enqueue($item, MoodleOutboxService::ACTION_ENROLL);
        }
    }

    protected function enqueue(StudyPlanItem $item, string $action): void
    {
        if (MoodleSyncContext::disabled()) {
            return;
        }

        $version = optional($item->updated_at)->timestamp ?? now()->timestamp;
        $dedupe = "study_plan_enrollment:{$item->id}:{$action}:{$version}";

        $userId = (int) ($item->studyPlan?->collageStudent?->user_id ?? 0);

        $this->outbox->enqueue(
            MoodleOutboxService::ENTITY_STUDY_PLAN_ENROLLMENT,
            (int) $item->id,
            (int) $item->tenant_id,
            $action,
            [
                'tenant_id' => (int) $item->tenant_id,
                'study_plan_id' => (int) $item->study_plan_id,
                'course_offering_id' => (int) $item->course_offering_id,
                'user_id' => $userId,
                'status' => $item->status,
                'deleted_at' => (($d = $item->getAttribute('deleted_at')) instanceof Carbon ? $d->toDateTimeString() : null),
            ],
            $dedupe,
        );
    }

    protected function shouldEnroll(StudyPlanItem $item): bool
    {
        if ($item->trashed()) {
            return false;
        }

        $status = strtolower(trim((string) $item->status));

        return in_array($status, self::ENROLLED_STATUSES, true);
    }

    protected function shouldUnenroll(StudyPlanItem $item): bool
    {
        if ($item->trashed()) {
            return true;
        }

        $status = strtolower(trim((string) $item->status));

        return in_array($status, self::UNENROLLED_STATUSES, true);
    }
}
