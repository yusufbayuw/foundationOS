<?php

namespace Modules\Counseling\Services;

use Modules\Counseling\Models\CounselingCase;
use Modules\School\Models\Attendance;
use Modules\School\Models\Student;
use Modules\School\Models\StudentGrade;
use Modules\School\Models\Violation;

class CounselingRiskScanner
{
    public function scanTenant(int $tenantId, int $violationThreshold = 3, float $attendanceAbsentPercent = 20.0, float $gradeBelow = 75.0): int
    {
        $created = 0;

        Student::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->each(function (Student $student) use ($violationThreshold, $attendanceAbsentPercent, $gradeBelow, &$created): void {
                if (! $this->studentNeedsFlag($student, $violationThreshold, $attendanceAbsentPercent, $gradeBelow)) {
                    return;
                }

                $exists = CounselingCase::query()
                    ->where('student_id', $student->getKey())
                    ->where('status', 'active')
                    ->exists();

                if ($exists) {
                    return;
                }

                CounselingCase::query()->create([
                    'tenant_id' => $student->tenant_id,
                    'organization_id' => $student->organization_id,
                    'student_id' => $student->getKey(),
                    'name' => 'Auto risk flag — '.$student->user?->name,
                    'status' => 'active',
                    'risk_level' => 'high',
                    'case_target_type' => Student::class,
                    'case_target_id' => $student->getKey(),
                    'description' => 'Created by counseling:scan-risk',
                ]);

                $created++;
            });

        return $created;
    }

    protected function studentNeedsFlag(Student $student, int $violationThreshold, float $attendanceAbsentPercent, float $gradeBelow): bool
    {
        $violations = Violation::query()->where('student_id', $student->getKey())->count();
        if ($violations >= $violationThreshold) {
            return true;
        }

        $totalAttendance = Attendance::query()->where('student_id', $student->getKey())->count();
        if ($totalAttendance > 0) {
            $absent = Attendance::query()
                ->where('student_id', $student->getKey())
                ->whereIn('status', ['absent', 'alpha'])
                ->count();
            if (($absent / $totalAttendance) * 100 >= $attendanceAbsentPercent) {
                return true;
            }
        }

        $lowGrades = StudentGrade::query()
            ->where('student_id', $student->getKey())
            ->where('score', '<', $gradeBelow)
            ->count();

        return $lowGrades >= 2;
    }
}
