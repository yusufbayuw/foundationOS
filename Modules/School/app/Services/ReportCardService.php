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
        $period = AcademicPeriod::findOrFail($academicPeriodId);

        // Identify class logic: fallback to checking any schedule or class link.
        // Here we just grab the class from an assessment or first class student link.
        $className = 'N/A';
        $classLink = ClassStudent::where('student_id', $studentId)
            ->with('schoolClass')
            ->first();

        if ($classLink && $classLink->schoolClass) {
            $className = $classLink->schoolClass->name;
        }

        $grades = StudentGrade::with(['assessment.subject'])
            ->where('student_id', $studentId)
            ->whereHas('assessment', function ($query) use ($academicPeriodId) {
                $query->where('academic_period_id', $academicPeriodId);
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

            $score = $grade->score ?? 0;
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

        $attendances = Attendance::where('student_id', $studentId)
            ->whereHas('schedule', function ($query) use ($academicPeriodId) {
                $query->where('academic_period_id', $academicPeriodId);
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

        $achievements = StudentAchievement::where('student_id', $studentId)
            ->when($start, fn ($q) => $q->where('event_date', '>=', $start))
            ->when($end, fn ($q) => $q->where('event_date', '<=', $end))
            ->orderBy('event_date', 'desc')
            ->get();

        $violations = Violation::where('student_id', $studentId)
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
