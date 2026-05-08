<?php

namespace Modules\Employee\Services;

use Carbon\Carbon;
use Modules\Employee\Enums\AttendanceStatus;
use Modules\Employee\Models\AttendanceLog;
use Modules\Employee\Models\Shift;

class AttendanceProcessingService
{
    /**
     * Calculate and save work_hours and overtime_hours for an attendance log.
     * Uses the assigned shift to determine standard hours and late threshold.
     */
    public function recalculate(AttendanceLog $log): AttendanceLog
    {
        if (! $log->check_in || ! $log->check_out) {
            return $log;
        }

        $checkIn = Carbon::parse($log->check_in);
        $checkOut = Carbon::parse($log->check_out);

        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            $checkOut->addDay();
        }

        $totalMinutes = $checkIn->diffInMinutes($checkOut);

        $breakMinutes = $log->shift?->break_duration_minutes ?? 60;
        $workedMinutes = max(0, $totalMinutes - $breakMinutes);

        $standardMinutes = $this->standardShiftMinutes($log->shift);
        $overtimeMinutes = max(0, $workedMinutes - $standardMinutes);

        $log->work_hours = round($workedMinutes / 60, 2);
        $log->overtime_hours = round($overtimeMinutes / 60, 2);

        if ($log->status === AttendanceStatus::Present || $log->status === null) {
            $log->status = $this->resolveStatus($log, $checkIn);
        }

        $log->save();

        return $log;
    }

    /**
     * Determine attendance status based on check-in time vs shift start.
     */
    private function resolveStatus(AttendanceLog $log, Carbon $checkIn): AttendanceStatus
    {
        if (! $log->shift) {
            return AttendanceStatus::Present;
        }

        $shiftStart = Carbon::parse($log->date->format('Y-m-d') . ' ' . $log->shift->start_time);
        $lateThresholdMinutes = 15;

        if ($checkIn->diffInMinutes($shiftStart, false) < -$lateThresholdMinutes) {
            return AttendanceStatus::Late;
        }

        return AttendanceStatus::Present;
    }

    /**
     * Get standard working minutes from a shift definition.
     * Accounts for night shifts that span midnight.
     */
    private function standardShiftMinutes(?Shift $shift): int
    {
        if (! $shift) {
            return 8 * 60; // default 8-hour workday
        }

        $start = Carbon::parse('2000-01-01 ' . $shift->start_time);
        $end = Carbon::parse('2000-01-01 ' . $shift->end_time);

        if ($shift->is_night_shift || $end->lessThan($start)) {
            $end->addDay();
        }

        $total = $start->diffInMinutes($end);

        return max(0, $total - ($shift->break_duration_minutes ?? 60));
    }

    /**
     * Count attendance statistics for a given employee and month/year.
     * Returns: working_days, working_hours, overtime_hours, leave_days, absent_days.
     */
    public function monthlyStats(int $employeeId, int $month, int $year): array
    {
        $logs = AttendanceLog::where('employee_id', $employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $workingDays = 0;
        $workingHours = 0.0;
        $overtimeHours = 0.0;
        $leaveDays = 0;
        $absentDays = 0;

        foreach ($logs as $log) {
            $status = $log->status instanceof AttendanceStatus
                ? $log->status
                : AttendanceStatus::tryFrom($log->status ?? '');

            if ($status?->countsAsAbsent()) {
                $absentDays++;
            } elseif ($status?->countsAsLeave()) {
                $leaveDays++;
            } else {
                $workingDays++;
                $workingHours += (float) $log->work_hours;
                $overtimeHours += (float) $log->overtime_hours;
            }
        }

        return [
            'working_days' => $workingDays,
            'working_hours' => round($workingHours, 2),
            'overtime_hours' => round($overtimeHours, 2),
            'leave_days' => $leaveDays,
            'absent_days' => $absentDays,
        ];
    }
}
