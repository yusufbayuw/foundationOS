<?php

namespace Modules\School\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\School\Models\Attendance;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Schedule;
use Modules\School\Models\Student;

class AttendanceRecapService
{
    /**
     * Get a recap of student attendance for a specific class, period, and month.
     *
     * @param  int|string  $tenantId
     * @param  int|string  $academicPeriodId
     * @param  int|string  $classId
     * @param  int  $month
     * @param  int  $year
     * @return Collection<int, object{
     *     student_id: int|string,
     *     nis: string|null,
     *     student_name: string,
     *     total_present: int,
     *     total_absent: int,
     *     total_sick: int,
     *     total_permission: int,
     *     total_late: int,
     *     total_days: int,
     *     percentage: float
     * }>
     */
    public function getStudentRecap(
        $tenantId,
        $academicPeriodId,
        $classId,
        $month,
        $year
    ): Collection {
        // Find students in the given class and period
        $students = Student::with(['user'])
            ->whereHas('classStudents', function (Builder $query) use ($classId, $academicPeriodId): void {
                /** @var Builder<ClassStudent> $query */
                $query->where('class_id', $classId)
                    ->where('academic_period_id', $academicPeriodId);
            })
            ->where('tenant_id', $tenantId)
            ->get();

        if ($students->isEmpty()) {
            return collect();
        }

        // Find attendances for these students in this month
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

        $recap = collect();

        foreach ($students as $student) {
            $studentAttendances = $attendances->where('student_id', $student->id);

            $present = 0;
            $absent = 0;
            $sick = 0;
            $permission = 0;
            $late = 0;

            foreach ($studentAttendances as $attendance) {
                $status = strtolower($attendance->status);

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
                    // We consider late as present for the purposes of percentage usually
                    $present++;
                } else {
                    $present++;
                }
            }

            $totalDays = $studentAttendances->count();
            // Percentage based on present vs total logged days
            $percentage = $totalDays > 0 ? (float) round(($present / $totalDays) * 100) : 0.0;

            $recap->push((object) [
                'student_id' => $student->id,
                'nis' => $student->nis,
                'student_name' => $student->user ? $student->user->name : ($student->nis ?? 'Unknown'),
                'total_present' => $present,
                'total_absent' => $absent,
                'total_sick' => $sick,
                'total_permission' => $permission,
                'total_late' => $late,
                'total_days' => $totalDays,
                'percentage' => $percentage,
            ]);
        }

        return $recap->sortBy('student_name')->values();
    }
}
