<?php

namespace App\Integrations\Moodle;

use App\Models\MoodleEnrollmentDrift;
use Modules\Core\Models\TenantSetting;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Student;

/**
 * Applies remediations for enrollment drifts detected by the reconciler.
 *
 * Modes (per tenant setting `moodle.enrollment_drift_auto_fix_mode`):
 *   - disabled       → never act, just mark for review
 *   - outbound_only  → fix Moodle-side only (missing_in_moodle, mismatched_role).
 *                      FOS-side drifts are recorded but not auto-applied
 *                      (avoids accidentally creating ClassStudent rows from
 *                      data the tenant didn't approve).
 *   - bidirectional  → also create FOS ClassStudent rows for missing_in_fos
 *                      drifts when a matching student exists.
 *
 * Default mode = outbound_only.
 */
class MoodleEnrollmentDriftFixer
{
    public const SETTING_KEY = 'enrollment_drift_auto_fix_mode';

    public const SETTING_GROUP = 'moodle';

    public const MODE_DISABLED = 'disabled';

    public const MODE_OUTBOUND_ONLY = 'outbound_only';

    public const MODE_BIDIRECTIONAL = 'bidirectional';

    public const RESOLUTION_OUTBOX_ENROL = 'outbox.enrol_user';

    public const RESOLUTION_OUTBOX_REASSIGN = 'outbox.reassign_role';

    public const RESOLUTION_FOS_CLASS_STUDENT_CREATED = 'fos.class_student_created';

    public const RESOLUTION_MARKED_FOR_REVIEW = 'marked_for_review';

    public function __construct(private readonly MoodleOutboxService $outbox) {}

    public function modeFor(int $tenantId): string
    {
        $setting = TenantSetting::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('group', self::SETTING_GROUP)
            ->where('key', self::SETTING_KEY)
            ->first();

        return $setting?->value ?: self::MODE_OUTBOUND_ONLY;
    }

    /**
     * Attempt to resolve the drift. Returns true when an action was taken
     * (either applied or marked for review).
     */
    public function fix(MoodleEnrollmentDrift $drift): bool
    {
        if ($drift->resolved_at !== null) {
            return false;
        }

        $mode = $this->modeFor((int) $drift->tenant_id);

        if ($mode === self::MODE_DISABLED) {
            $this->markForReview($drift, 'auto-fix disabled for tenant');

            return true;
        }

        return match ($drift->drift_type) {
            MoodleEnrollmentDrift::TYPE_MISSING_IN_MOODLE => $this->fixMissingInMoodle($drift),
            MoodleEnrollmentDrift::TYPE_MISMATCHED_ROLE => $this->fixMismatchedRole($drift),
            MoodleEnrollmentDrift::TYPE_MISSING_IN_FOS => $this->fixMissingInFos($drift, $mode),
            default => false,
        };
    }

    protected function fixMissingInMoodle(MoodleEnrollmentDrift $drift): bool
    {
        $userId = (int) $drift->user_id;
        $this->outbox->enqueue(
            entityType: MoodleOutboxService::ENTITY_ENROLLMENT,
            entityId: $userId,
            tenantId: (int) $drift->tenant_id,
            action: MoodleOutboxService::ACTION_ENROLL,
            payload: [
                'course_moodle_id' => $drift->course_moodle_id,
                'user_moodle_idnumber' => $drift->user_moodle_idnumber,
                'role' => $drift->expected_role ?? 'student',
                'class_id' => $drift->class_id,
            ],
            dedupeKey: "drift:enrol:{$drift->id}",
        );

        $this->resolve($drift, self::RESOLUTION_OUTBOX_ENROL);

        return true;
    }

    protected function fixMismatchedRole(MoodleEnrollmentDrift $drift): bool
    {
        $this->outbox->enqueue(
            entityType: MoodleOutboxService::ENTITY_ENROLLMENT,
            entityId: (int) $drift->user_id,
            tenantId: (int) $drift->tenant_id,
            action: MoodleOutboxService::ACTION_ENROLL,
            payload: [
                'course_moodle_id' => $drift->course_moodle_id,
                'user_moodle_idnumber' => $drift->user_moodle_idnumber,
                'role' => $drift->expected_role ?? 'student',
                'previous_role' => $drift->actual_role,
                'class_id' => $drift->class_id,
            ],
            dedupeKey: "drift:reassign:{$drift->id}",
        );

        $this->resolve($drift, self::RESOLUTION_OUTBOX_REASSIGN);

        return true;
    }

    protected function fixMissingInFos(MoodleEnrollmentDrift $drift, string $mode): bool
    {
        if ($mode !== self::MODE_BIDIRECTIONAL) {
            $this->markForReview($drift, 'inbound fix requires bidirectional mode');

            return true;
        }

        $student = Student::query()
            ->where('tenant_id', $drift->tenant_id)
            ->where('user_id', $drift->user_id)
            ->first();

        if (! $student || ! $drift->class_id) {
            $this->markForReview($drift, 'no matching FOS Student / class_id missing');

            return true;
        }

        ClassStudent::query()->create([
            'tenant_id' => $drift->tenant_id,
            'class_id' => $drift->class_id,
            'student_id' => $student->id,
            'status' => 'active',
            'entry_date' => now()->toDateString(),
            'entry_type' => 'moodle_drift_autofix',
        ]);

        $this->resolve($drift, self::RESOLUTION_FOS_CLASS_STUDENT_CREATED);

        return true;
    }

    protected function markForReview(MoodleEnrollmentDrift $drift, string $reason): void
    {
        $drift->forceFill([
            'resolution' => self::RESOLUTION_MARKED_FOR_REVIEW,
            'meta' => array_merge((array) $drift->meta, ['review_reason' => $reason]),
        ])->save();
    }

    protected function resolve(MoodleEnrollmentDrift $drift, string $resolution): void
    {
        $drift->forceFill([
            'resolved_at' => now(),
            'resolution' => $resolution,
        ])->save();
    }
}
