<?php

namespace Modules\Campus\Services;

use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\StudyPlanItem;

/**
 * Recompute cached GPA (IPK) for a CollageStudent based on published StudyResults.
 */
class CampusGpaCalculator
{
    /**
     * Recalculate GPA across all published study results for this student.
     * Returns the new GPA.
     */
    public function recalculateForStudent(int $collageStudentId): float
    {
        $items = StudyPlanItem::withoutTenantScope()
            ->whereHas('studyPlan', fn ($q) => $q->where('collage_student_id', $collageStudentId))
            ->with('studyResult')
            ->get();

        $totalWeighted = 0.0;
        $totalCredits = 0;

        foreach ($items as $item) {
            $result = $item->studyResult;
            if (! $result || $result->grade_point === null) {
                continue;
            }

            $credits = (int) ($item->credits ?? 0);
            if ($credits <= 0) {
                continue;
            }

            $totalWeighted += $credits * (float) $result->grade_point;
            $totalCredits += $credits;
        }

        $gpa = $totalCredits > 0 ? round($totalWeighted / $totalCredits, 2) : 0.0;

        $student = CollageStudent::withoutTenantScope()->find($collageStudentId);
        if ($student) {
            $student->forceFill([
                'gpa_cached' => $gpa,
                'gpa_recalculated_at' => now(),
            ])->save();
        }

        return $gpa;
    }
}
