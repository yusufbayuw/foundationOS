<?php

namespace App\Integrations\Moodle;

use Modules\Campus\Models\StudyPlan;
use Modules\Campus\Models\StudyPlanItem;

/**
 * Bridges Campus KRS (StudyPlan + StudyPlanItem) to Moodle course-offering
 * enrollment via the outbox.
 *
 * Enrollment is keyed by `fos_offering_{offering_id}` (see
 * MoodleCampusCatalogSyncService); the receiving worker / Moodle plugin
 * resolves the actual moodle course id from that idnumber.
 */
class MoodleStudyPlanSyncService
{
    public function __construct(private readonly MoodleOutboxService $outbox) {}

    public function enrollAllForPlan(StudyPlan $plan): int
    {
        $count = 0;
        foreach ($plan->fresh(['items.courseOffering'])->items as $item) {
            if ($this->enrollItem($item)) {
                $count++;
            }
        }

        return $count;
    }

    public function unenrollAllForPlan(StudyPlan $plan): int
    {
        $count = 0;
        foreach ($plan->fresh(['items'])->items as $item) {
            if ($this->unenrollItem($item)) {
                $count++;
            }
        }

        return $count;
    }

    public function enrollItem(StudyPlanItem $item): bool
    {
        if ($item->course_offering_id === null) {
            return false;
        }

        $this->outbox->enqueue(
            entityType: MoodleOutboxService::ENTITY_ENROLLMENT,
            entityId: (int) $item->getKey(),
            tenantId: (int) $item->tenant_id,
            action: MoodleOutboxService::ACTION_ENROLL,
            payload: [
                'study_plan_id' => $item->study_plan_id,
                'offering_idnumber' => MoodleCampusCatalogSyncService::offeringIdnumber((int) $item->course_offering_id),
                'course_offering_id' => $item->course_offering_id,
                'role' => 'student',
                'source' => 'study_plan',
            ],
            dedupeKey: "krs:enrol:{$item->id}",
        );

        return true;
    }

    public function unenrollItem(StudyPlanItem $item): bool
    {
        if ($item->course_offering_id === null) {
            return false;
        }

        $this->outbox->enqueue(
            entityType: MoodleOutboxService::ENTITY_ENROLLMENT,
            entityId: (int) $item->getKey(),
            tenantId: (int) $item->tenant_id,
            action: MoodleOutboxService::ACTION_UNENROLL,
            payload: [
                'study_plan_id' => $item->study_plan_id,
                'offering_idnumber' => MoodleCampusCatalogSyncService::offeringIdnumber((int) $item->course_offering_id),
                'course_offering_id' => $item->course_offering_id,
                'source' => 'study_plan',
            ],
            dedupeKey: "krs:unenrol:{$item->id}",
        );

        return true;
    }

    /**
     * Enqueue enrollment for every approved StudyPlanItem in the given
     * academic period. Used by semester rollover command.
     */
    public function bulkEnrollSemester(int $academicPeriodId): int
    {
        $count = 0;

        StudyPlan::query()
            ->where('academic_period_id', $academicPeriodId)
            ->where('status', 'approved')
            ->with('items')
            ->chunkById(100, function ($plans) use (&$count): void {
                foreach ($plans as $plan) {
                    $count += $this->enrollAllForPlan($plan);
                }
            });

        return $count;
    }
}
