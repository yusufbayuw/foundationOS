<?php

namespace Modules\School\Services;

use Illuminate\Support\Collection;
use Modules\Core\Models\AcademicPeriod;
use Modules\School\Models\Assessment;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Models\StudentGrade;

class ClassGradeLedgerService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(SchoolClass $schoolClass, int $academicPeriodId): array
    {
        $period = AcademicPeriod::query()->findOrFail($academicPeriodId);

        $assessments = Assessment::query()
            ->where('class_id', $schoolClass->getKey())
            ->where('academic_period_id', $academicPeriodId)
            ->with('subject')
            ->orderBy('subject_id')
            ->orderBy('name')
            ->get();

        $students = Student::query()
            ->with('user')
            ->whereHas('classStudents', function ($query) use ($schoolClass, $academicPeriodId): void {
                $query->where('class_id', $schoolClass->getKey())
                    ->where('academic_period_id', $academicPeriodId);
            })
            ->where('tenant_id', $schoolClass->tenant_id)
            ->get()
            ->sortBy(fn (Student $student): string => (string) ($student->user?->name ?? $student->nis ?? ''))
            ->values();

        $gradesByStudent = StudentGrade::query()
            ->whereIn('student_id', $students->pluck('id'))
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $rows = $students->map(function (Student $student) use ($assessments, $gradesByStudent): array {
            /** @var Collection<int, StudentGrade> $studentGrades */
            $studentGrades = $gradesByStudent->get($student->getKey(), collect());
            $scores = [];

            foreach ($assessments as $assessment) {
                $grade = $studentGrades->firstWhere('assessment_id', $assessment->getKey());
                $scores[$assessment->getKey()] = $grade?->score ?? $grade?->final_score;
            }

            return [
                'student' => $student,
                'student_name' => $student->user?->name ?? $student->nis ?? '-',
                'nis' => $student->nis,
                'scores' => $scores,
            ];
        });

        return [
            'schoolClass' => $schoolClass,
            'period' => $period,
            'assessments' => $assessments,
            'rows' => $rows,
            'showSignature' => true,
            'signatureLabel' => 'Wali Kelas',
        ];
    }

    public function filename(SchoolClass $schoolClass, AcademicPeriod $period): string
    {
        return sprintf(
            'Buku_Nilai_%s_%s.pdf',
            str_replace(' ', '_', $schoolClass->name ?? 'Kelas'),
            str_replace(' ', '_', $period->name ?? 'Periode'),
        );
    }
}
