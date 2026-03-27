<?php

namespace Modules\School\Services;

use Illuminate\Support\Collection;
use Modules\School\Models\Attendance;
use Modules\School\Models\Student;

class AttendanceRecapService
{
    /**
     * Get a recap of student attendance for a specific class, period, and month.
     *
     * @param int|string $tenantId
     * @param int|string $academicPeriodId
     * @param int|string $classId
     * @param int $month
     * @param int $year
     * @return Collection
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
            ->whereHas('classStudents', function ($query) use ($classId, $academicPeriodId) {
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
            ->whereHas('schedule', function ($query) use ($classId, $academicPeriodId) {
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
            $percentage = $totalDays > 0 ? round(($present / $totalDays) * 100) : 0;

            $recap->push((object)[
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
