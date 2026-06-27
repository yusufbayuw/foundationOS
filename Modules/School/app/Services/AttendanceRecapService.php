<?php

namespace Modules\School\Services;

use App\Support\TypedValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\School\Models\Attendance;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Schedule;
use Modules\School\Models\Student;
use Modules\School\Support\AttendanceRecapRow;

class AttendanceRecapService
{
    /**
     * Get a recap of student attendance for a specific class, period, and month.
     *
     * @return Collection<int, AttendanceRecapRow>
     */
    public function getStudentRecap(
        int|string $tenantId,
        int|string $academicPeriodId,
        int|string $classId,
        int $month,
        int $year
    ): Collection {
        // Find students in the given class and period
        $students = Student::query()
            ->with(['user'])
            ->whereHas('classStudents', function (Builder $query) use ($classId, $academicPeriodId): void {
                /** @var Builder<ClassStudent> $query */
                $query->where('class_id', $classId)
                    ->where('academic_period_id', $academicPeriodId);
            })
            ->where('tenant_id', $tenantId)
            ->get();

        $rows = [];

        if ($students->isNotEmpty()) {
            // Find attendances for these students in this month.
            $attendances = Attendance::whereIn('student_id', $students->pluck('id'))
                ->where('tenant_id', $tenantId)
                ->whereHas('schedule', function (Builder $query) use ($classId, $academicPeriodId): void {
                    /** @var Builder<Schedule> $query */
                    $query->where('class_id', $classId)
                        ->where('academic_period_id', $academicPeriodId);
                })
                ->whereMonth('attendance_date', $month)
                ->whereYear('attendance_date', $year)
                ->get();

            foreach ($students as $student) {
                $studentAttendances = $attendances->where('student_id', $student->id);

                $present = 0;
                $absent = 0;
                $sick = 0;
                $permission = 0;
                $late = 0;

                foreach ($studentAttendances as $attendance) {
                    $status = strtolower(TypedValue::string($attendance->status));

                    if (in_array($status, ['present', 'hadir', 'mengikuti'])) {
                        $present++;
                    } elseif (in_array($status, ['absent', 'alpa', 'tidak hadir'])) {
                        $absent++;
                    } elseif (in_array($status, ['sick', 'sakit'])) {
                        $sick++;
                    } elseif (in_array($status, ['permission', 'izin', 'ijin'])) {
                        $permission++;
                    } elseif (in_array($status, ['late', 'terlambat'])) {
                        $late++;
                        // We consider late as present for the purposes of percentage usually.
                        $present++;
                    } else {
                        $present++;
                    }
                }

                $totalDays = $studentAttendances->count();
                // Percentage based on present vs total logged days.
                $percentage = $totalDays > 0 ? (float) round(($present / $totalDays) * 100) : 0.0;
                $studentName = $student->user?->name;
                $studentId = $student->getKey();

                $rows[] = new AttendanceRecapRow(
                    student_id: is_int($studentId) || is_string($studentId) ? $studentId : 0,
                    nis: $student->nis,
                    student_name: $studentName !== null && $studentName !== '' ? $studentName : ($student->nis ?? 'Unknown'),
                    total_present: $present,
                    total_absent: $absent,
                    total_sick: $sick,
                    total_permission: $permission,
                    total_late: $late,
                    total_days: $totalDays,
                    percentage: $percentage,
                );
            }
        }

        return (new Collection($rows))->sortBy('student_name')->values();
    }
}
