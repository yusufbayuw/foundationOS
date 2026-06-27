<?php

namespace App\Integrations\Moodle;

use App\Models\MoodleClassCourseMapping;
use App\Models\MoodleEnrollmentDrift;
use App\Support\TypedValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Student;

/**
 * Compares Moodle-side enrollment to FOS-side class_students for a class
 * and records drifts in `moodle_enrollment_drifts`.
 *
 * The Moodle fetch is injected (an array of enrolled users from
 * `core_enrol_get_enrolled_users`) so the service stays testable and
 * the actual API call lives in the command/job layer.
 *
 * Expected Moodle user payload shape (subset of core_enrol_get_enrolled_users):
 *   ['id' => int, 'idnumber' => 'fos_user_{id}', 'roles' => [['shortname' => 'student'], ...]]
 */
class MoodleEnrollmentReconciler
{
    public const FOS_USER_PREFIX = 'fos_user_';

    /**
     * Detect drifts for a single class ↔ course mapping.
     *
     * @param  array<int, array{id:int,idnumber?:string,roles?:array<int, array{shortname:string}>}>  $moodleEnrollment
     * @param  bool  $dryRun  when true, do not insert drift rows; only return them
     * @return Collection<int, MoodleEnrollmentDrift>
     */
    public function reconcileMapping(MoodleClassCourseMapping $mapping, array $moodleEnrollment, bool $dryRun = false): Collection
    {
        $tenantId = (int) $mapping->tenant_id;
        $classId = (int) $mapping->class_id;

        // FOS side: active enrollments → expected idnumbers (fos_user_{user_id}).
        $fosUserIds = ClassStudent::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('class_id', $classId)
            ->whereIn('status', ['active', 'enrolled'])
            ->pluck('student_id');

        $fosUserMap = Student::query()
            ->whereIn('id', $fosUserIds)
            ->pluck('user_id', 'id')
            ->filter()
            ->mapWithKeys(static function (mixed $userId, mixed $studentId): array {
                $normalizedUserId = TypedValue::int($userId);

                return [
                    self::FOS_USER_PREFIX.$normalizedUserId => $normalizedUserId,
                ];
            });

        // Moodle side: keyed by idnumber.
        $moodleByIdnumber = collect($moodleEnrollment)
            ->filter(fn (array $u) => ! empty($u['idnumber']))
            ->keyBy('idnumber');

        /** @var Collection<int, MoodleEnrollmentDrift> $drifts */
        $drifts = collect();
        $now = now();

        // missing_in_moodle: present in FOS, absent in Moodle.
        foreach ($fosUserMap as $idnumber => $userId) {
            if (! $moodleByIdnumber->has($idnumber)) {
                $drifts->push($this->record($mapping, $now, [
                    'user_id' => $userId,
                    'user_moodle_idnumber' => $idnumber,
                    'drift_type' => MoodleEnrollmentDrift::TYPE_MISSING_IN_MOODLE,
                    'expected_role' => 'student',
                ], $dryRun));
            }
        }

        // missing_in_fos: present in Moodle, absent in FOS.
        // Only consider users that look like FOS-managed ones (fos_user_* prefix).
        foreach ($moodleByIdnumber as $idnumber => $payload) {
            if (! str_starts_with((string) $idnumber, self::FOS_USER_PREFIX)) {
                continue;
            }

            if (! $fosUserMap->has($idnumber)) {
                $drifts->push($this->record($mapping, $now, [
                    'user_id' => (int) substr((string) $idnumber, strlen(self::FOS_USER_PREFIX)),
                    'user_moodle_id' => (int) $payload['id'],
                    'user_moodle_idnumber' => $idnumber,
                    'drift_type' => MoodleEnrollmentDrift::TYPE_MISSING_IN_FOS,
                    'actual_role' => $this->primaryRole($payload),
                ], $dryRun));

                continue;
            }

            // mismatched_role: in both, but Moodle role != expected 'student'.
            $actualRole = $this->primaryRole($payload);
            if ($actualRole !== null && $actualRole !== 'student') {
                $drifts->push($this->record($mapping, $now, [
                    'user_id' => $fosUserMap[$idnumber],
                    'user_moodle_id' => (int) $payload['id'],
                    'user_moodle_idnumber' => $idnumber,
                    'drift_type' => MoodleEnrollmentDrift::TYPE_MISMATCHED_ROLE,
                    'expected_role' => 'student',
                    'actual_role' => $actualRole,
                ], $dryRun));
            }
        }

        return $drifts;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function record(MoodleClassCourseMapping $mapping, Carbon $now, array $payload, bool $dryRun): MoodleEnrollmentDrift
    {
        $row = array_merge([
            'tenant_id' => TypedValue::int($mapping->tenant_id),
            'class_id' => TypedValue::int($mapping->class_id),
            'course_moodle_id' => $mapping->moodle_course_id,
            'course_moodle_idnumber' => $mapping->moodle_course_idnumber,
            'detected_at' => $now,
        ], $payload);

        if ($dryRun) {
            return new MoodleEnrollmentDrift($row);
        }

        $drift = new MoodleEnrollmentDrift;
        $drift->forceFill($row);
        $drift->save();

        return $drift;
    }

    /**
     * @param  array{roles?:array<int, array{shortname:string}>}  $payload
     */
    protected function primaryRole(array $payload): ?string
    {
        $roles = $payload['roles'] ?? [];
        $first = $roles[0] ?? null;

        return is_array($first) ? $first['shortname'] : null;
    }
}
