<?php

namespace Modules\School\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Modules\School\Models\Schedule;

class ScheduleConflictChecker
{
    /**
     * Check for schedule conflicts (teacher or class overlap).
     *
     * @param  array  $data  The schedule data containing class_id, teacher_id, day_of_week, start_time, end_time, academic_period_id
     * @param  int|null  $excludeId  ID of the schedule to exclude from checks (e.g., when updating)
     * @return array List of conflict messages. Empty array if no conflicts.
     */
    public function checkConflicts(array $data, ?int $excludeId = null): array
    {
        $conflicts = [];

        // We need all these fields to check for conflicts
        if (
            empty($data['day_of_week']) ||
            empty($data['start_time']) ||
            empty($data['end_time']) ||
            empty($data['academic_period_id'])
        ) {
            return $conflicts;
        }

        $query = Schedule::query()
            ->where('academic_period_id', $data['academic_period_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where(function (Builder $q) use ($data) {
                // Check for overlapping times
                // A new schedule overlaps an existing schedule if:
                // New Start < Existing End AND New End > Existing Start
                $q->where('start_time', '<', $data['end_time'])
                    ->where('end_time', '>', $data['start_time']);
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // Check if teacher is already booked
        if (! empty($data['teacher_id'])) {
            $teacherConflict = (clone $query)
                ->where('teacher_id', $data['teacher_id'])
                ->with(['subject', 'schoolClass'])
                ->first();

            if ($teacherConflict) {
                $subjectName = $teacherConflict->subject ? $teacherConflict->subject->name : 'Mata Pelajaran';
                $className = $teacherConflict->schoolClass ? $teacherConflict->schoolClass->name : 'Kelas';
                $startTime = Carbon::parse($teacherConflict->start_time)->format('H:i');
                $endTime = Carbon::parse($teacherConflict->end_time)->format('H:i');

                $conflicts[] = "Guru ini sudah mengajar {$subjectName} di {$className} pada waktu {$startTime} - {$endTime}.";
            }
        }

        // Check if class already has another subject
        if (! empty($data['class_id'])) {
            $classConflict = (clone $query)
                ->where('class_id', $data['class_id'])
                ->with(['subject', 'teacher'])
                ->first();

            if ($classConflict) {
                $subjectName = $classConflict->subject ? $classConflict->subject->name : 'Mata Pelajaran';
                $teacherName = $classConflict->teacher->user->name ?? 'Guru';
                $startTime = Carbon::parse($classConflict->start_time)->format('H:i');
                $endTime = Carbon::parse($classConflict->end_time)->format('H:i');

                $conflicts[] = "Kelas ini sudah ada jadwal {$subjectName} dengan {$teacherName} pada waktu {$startTime} - {$endTime}.";
            }
        }

        return $conflicts;
    }
}
