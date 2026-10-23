<?php

namespace Modules\School\Services;

use Modules\Core\Models\AcademicPeriod;
use Modules\School\Models\Attendance;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Student;
use Modules\School\Models\StudentAchievement;
use Modules\School\Models\StudentGrade;
use Modules\School\Models\Violation;

class ReportCardService
{
    /**
     * Generate report card data for a student in a specific academic period.
     */
    public function generate(int $studentId, int $academicPeriodId): array
    {
        $student = Student::with(['user'])->findOrFail($studentId);
        $period = AcademicPeriod::query()
            ->where('tenant_id', $student->tenant_id)
            ->findOrFail($academicPeriodId);

        // A historical report must use the class assigned in this period, not
        // an arbitrary first class from another academic year.
        $className = 'N/A';
        $classLink = ClassStudent::query()
            ->where('tenant_id', $student->tenant_id)
            ->where('student_id', $studentId)
            ->where('academic_period_id', $academicPeriodId)
            ->whereHas('schoolClass', fn ($query) => $query
                ->where('tenant_id', $student->tenant_id)
                ->where('academic_period_id', $academicPeriodId))
            ->with('schoolClass')
            ->orderByDesc('entry_date')
            ->first();

        if ($classLink && $classLink->schoolClass) {
            $className = $classLink->schoolClass->name;
        }

        $grades = StudentGrade::with(['assessment.subject'])
            ->where('tenant_id', $student->tenant_id)
            ->where('student_id', $studentId)
            ->whereHas('assessment', function ($query) use ($academicPeriodId, $student) {
                $query->where('tenant_id', $student->tenant_id)
                    ->where('academic_period_id', $academicPeriodId);
            })
            ->get();

        $subjects = [];
        $totalScore = 0;
        $subjectCount = 0;

        foreach ($grades as $grade) {
            $assessment = $grade->assessment;
            if (! $assessment || ! $assessment->subject) {
                continue;
            }

            $subjectId = $assessment->subject_id;
            if (! isset($subjects[$subjectId])) {
                $subjects[$subjectId] = [
                    'name' => $assessment->subject->name,
                    'assessments' => [],
                    'average' => 0,
                    'total_score' => 0,
                    'count' => 0,
                ];
            }

            // An ungraded assessment is not a score of zero.
            $score = $grade->score ?? $grade->final_score;
            if ($score === null) {
                continue;
            }
            $subjects[$subjectId]['assessments'][] = [
                'type' => $assessment->type,
                'name' => $assessment->name,
                'score' => $score,
            ];

            $subjects[$subjectId]['total_score'] += $score;
            $subjects[$subjectId]['count']++;
        }

        foreach ($subjects as $id => &$subjectData) {
            if ($subjectData['count'] > 0) {
                $subjectData['average'] = round($subjectData['total_score'] / $subjectData['count'], 2);
            }
            $totalScore += $subjectData['average'];
            $subjectCount++;
        }

        $overallAverage = $subjectCount > 0 ? round($totalScore / $subjectCount, 2) : 0;

        $attendances = Attendance::where('tenant_id', $student->tenant_id)
            ->where('student_id', $studentId)
            ->whereHas('schedule', function ($query) use ($academicPeriodId, $student) {
                $query->where('tenant_id', $student->tenant_id)
                    ->where('academic_period_id', $academicPeriodId);
            })
            ->get();

        $attendanceSummary = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'sick' => $attendances->where('status', 'sick')->count(),
            'permission' => $attendances->where('status', 'permission')->count(),
        ];

        $start = $period->start_date;
        $end = $period->end_date;

        $achievements = StudentAchievement::where('tenant_id', $student->tenant_id)
            ->where('student_id', $studentId)
            ->when($start, fn ($q) => $q->where('event_date', '>=', $start))
            ->when($end, fn ($q) => $q->where('event_date', '<=', $end))
            ->orderBy('event_date', 'desc')
            ->get();

        $violations = Violation::where('tenant_id', $student->tenant_id)
            ->where('student_id', $studentId)
            ->when($start, fn ($q) => $q->where('date', '>=', $start))
            ->when($end, fn ($q) => $q->where('date', '<=', $end))
            ->orderBy('date', 'desc')
            ->get();

        return [
            'student' => $student,
            'period' => $period,
            'class_name' => $className,
            'subjects' => $subjects,
            'overall_average' => $overallAverage,
            'attendance' => $attendanceSummary,
            'achievements' => $achievements,
            'violations' => $violations,
        ];
    }
}
