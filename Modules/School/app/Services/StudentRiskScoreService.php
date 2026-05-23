<?php

namespace Modules\School\Services;

use Modules\Counseling\Models\CounselingCase;
use Modules\Finance\Models\StudentInvoice;
use Modules\School\Models\Attendance;
use Modules\School\Models\Student;
use Modules\School\Models\StudentGrade;
use Modules\School\Models\StudentRiskScore;
use Modules\School\Models\Violation;

class StudentRiskScoreService
{
    /**
     * @return array{
     *     composite_score: int,
     *     academic_score: int,
     *     financial_score: int,
     *     behavioral_score: int,
     *     health_score: int,
     *     attendance_score: int,
     *     is_at_risk: bool,
     * }
     */
    public function computeForStudent(Student $student): array
    {
        $attendanceScore = $this->attendanceDimension($student);
        $academicScore = $this->academicDimension($student);
        $behavioralScore = $this->behavioralDimension($student);
        $financialScore = $this->financialDimension($student);
        $healthScore = $this->healthDimension($student);

        $composite = (int) round(
            ($attendanceScore + $academicScore + $behavioralScore + $financialScore + $healthScore) / 5
        );

        $hasHighBkCase = CounselingCase::query()
            ->where('student_id', $student->getKey())
            ->where('risk_level', 'high')
            ->exists();

        $isAtRisk = $composite >= 70 || $hasHighBkCase;

        return [
            'composite_score' => $composite,
            'academic_score' => $academicScore,
            'financial_score' => $financialScore,
            'behavioral_score' => $behavioralScore,
            'health_score' => $healthScore,
            'attendance_score' => $attendanceScore,
            'is_at_risk' => $isAtRisk,
        ];
    }

    public function recomputeAndStore(Student $student): StudentRiskScore
    {
        $scores = $this->computeForStudent($student);

        return StudentRiskScore::query()->updateOrCreate(
            ['student_id' => $student->getKey()],
            array_merge($scores, [
                'tenant_id' => $student->tenant_id,
                'computed_at' => now(),
            ]),
        );
    }

    protected function attendanceDimension(Student $student): int
    {
        $total = Attendance::query()->where('student_id', $student->getKey())->count();
        if ($total === 0) {
            return 0;
        }

        $absent = Attendance::query()
            ->where('student_id', $student->getKey())
            ->whereIn('status', ['absent', 'alpha', 'sick', 'permission'])
            ->count();

        $absentRate = ($absent / $total) * 100;

        return (int) min(100, round($absentRate * 2));
    }

    protected function academicDimension(Student $student): int
    {
        $avg = StudentGrade::query()
            ->where('student_id', $student->getKey())
            ->avg('score');

        if ($avg === null) {
            return 0;
        }

        return (int) max(0, min(100, round((75 - (float) $avg) * 2)));
    }

    protected function behavioralDimension(Student $student): int
    {
        $count = Violation::query()->where('student_id', $student->getKey())->count();

        return (int) min(100, $count * 15);
    }

    protected function financialDimension(Student $student): int
    {
        $overdue = StudentInvoice::query()
            ->where('invoiceable_type', Student::class)
            ->where('invoiceable_id', $student->getKey())
            ->where('status', 'overdue')
            ->count();

        return (int) min(100, $overdue * 25);
    }

    protected function healthDimension(Student $student): int
    {
        $notes = $student->health_notes ?? [];

        return empty($notes) ? 0 : 20;
    }
}
