<?php

namespace App\Integrations\Moodle;

use App\Models\MoodleOfferingMapping;
use App\Support\TypedValue;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\CoursePrerequisite;

/**
 * Materializes catalogue mappings for Moodle:
 *   - Course → Moodle Course Template (kept once per tenant+course,
 *     idnumber = fos_template_{tenant_id}_{course_id}).
 *   - CourseOffering → Moodle Course instance per semester
 *     (idnumber = fos_offering_{offering_id}).
 *
 * Prerequisites are passed as hints inside the outbox payload
 * (`restrictions[]`) — Moodle's access-restriction API is limited via web
 * service, so this is a POC hint; the receiving worker / Moodle plugin
 * can interpret them later.
 *
 * When `moodle.enabled=false`, mapping rows are still upserted (so the
 * catalog is reproducible) but no outbox row is enqueued.
 */
class MoodleCampusCatalogSyncService
{
    public function __construct(private readonly MoodleOutboxService $outbox) {}

    public static function templateIdnumber(int $tenantId, int $courseId): string
    {
        return "fos_template_{$tenantId}_{$courseId}";
    }

    public static function offeringIdnumber(int $offeringId): string
    {
        return "fos_offering_{$offeringId}";
    }

    public function syncCourseTemplate(Course $course): MoodleOfferingMapping
    {
        $courseId = TypedValue::int($course->getKey());
        $tenantId = TypedValue::int($course->tenant_id);
        $idnumber = self::templateIdnumber($tenantId, $courseId);

        $mapping = MoodleOfferingMapping::query()->updateOrCreate(
            ['idnumber' => $idnumber],
            [
                'tenant_id' => $course->tenant_id,
                'course_id' => $course->getKey(),
                'course_offering_id' => null,
                'kind' => MoodleOfferingMapping::KIND_TEMPLATE,
                'is_active' => (bool) ($course->is_active ?? true),
                'meta' => [
                    'code' => $course->code,
                    'name' => $course->name,
                    'credits' => (int) $course->credits,
                ],
            ],
        );

        $this->outbox->enqueue(
            entityType: MoodleOutboxService::ENTITY_COURSE,
            entityId: $courseId,
            tenantId: $tenantId,
            action: MoodleOutboxService::ACTION_UPSERT,
            payload: [
                'kind' => MoodleOfferingMapping::KIND_TEMPLATE,
                'idnumber' => $idnumber,
                'code' => $course->code,
                'name' => $course->name,
                'description' => $course->description,
                'category_hint' => "fos_template_{$course->tenant_id}",
            ],
            dedupeKey: "campus:template:{$course->tenant_id}:{$course->id}",
        );

        return $mapping;
    }

    public function syncCourseOffering(CourseOffering $offering): MoodleOfferingMapping
    {
        $offering = $offering->fresh(['course']);
        if ($offering === null) {
            throw new \RuntimeException('Course offering no longer exists.');
        }

        $offeringId = TypedValue::int($offering->getKey());
        $tenantId = TypedValue::int($offering->tenant_id);
        $courseId = TypedValue::int($offering->course_id);
        $idnumber = self::offeringIdnumber($offeringId);

        $mapping = MoodleOfferingMapping::query()->updateOrCreate(
            ['idnumber' => $idnumber],
            [
                'tenant_id' => $offering->tenant_id,
                'course_id' => $offering->course_id,
                'course_offering_id' => $offering->getKey(),
                'kind' => MoodleOfferingMapping::KIND_OFFERING,
                'is_active' => $offering->status !== 'cancelled',
                'meta' => [
                    'class_code' => $offering->class_code,
                    'academic_period_id' => $offering->academic_period_id,
                    'capacity' => (int) $offering->capacity,
                    'delivery_mode' => $offering->delivery_mode,
                ],
            ],
        );

        $restrictions = $this->collectPrerequisites($tenantId, $courseId);

        $this->outbox->enqueue(
            entityType: MoodleOutboxService::ENTITY_COURSE,
            entityId: $offeringId,
            tenantId: $tenantId,
            action: MoodleOutboxService::ACTION_UPSERT,
            payload: [
                'kind' => MoodleOfferingMapping::KIND_OFFERING,
                'idnumber' => $idnumber,
                'template_idnumber' => self::templateIdnumber($tenantId, $courseId),
                'class_code' => $offering->class_code,
                'academic_period_id' => $offering->academic_period_id,
                'category_hint' => "fos_semester_{$offering->academic_period_id}",
                'restrictions' => $restrictions,
            ],
            dedupeKey: "campus:offering:{$offering->id}",
        );

        return $mapping;
    }

    /**
     * @return array<int, array{prerequisite_idnumber:string,min_grade:?float,is_strict:bool}>
     */
    protected function collectPrerequisites(int $tenantId, int $courseId): array
    {
        return CoursePrerequisite::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('course_id', $courseId)
            ->get()
            ->map(fn (CoursePrerequisite $p) => [
                'prerequisite_idnumber' => self::templateIdnumber($tenantId, (int) $p->prerequisite_course_id),
                'min_grade' => $p->min_grade !== null ? (float) $p->min_grade : null,
                'is_strict' => (bool) $p->is_strict,
            ])
            ->all();
    }
}
