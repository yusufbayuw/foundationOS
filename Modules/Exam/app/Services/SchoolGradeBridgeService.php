<?php

namespace Modules\Exam\Services;

use Illuminate\Support\Facades\Schema;
use Modules\Exam\Contracts\GradeBridgeInterface;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\GradeSyncMode;
use Modules\Exam\Models\ExamAttemptSync;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamResult;
use Modules\Exam\Support\GradebookExportOutcome;
use Modules\School\Models\Assessment;
use Modules\School\Models\Student;
use Modules\School\Models\StudentGrade;

class SchoolGradeBridgeService implements GradeBridgeInterface
{
    public function isGradebookAvailable(): bool
    {
        return class_exists(StudentGrade::class)
            && class_exists(Assessment::class)
            && Schema::hasTable('student_grades')
            && Schema::hasTable('assessments');
    }

    public function supports(ExamDefinition $definition): bool
    {
        return $definition->exam_academic_context === ExamAcademicContext::School
            && $definition->grade_sync_mode === GradeSyncMode::PushToStudentGrade;
    }

    public function syncAttempt(ExamDefinition $definition, ExamAttemptSync $attemptSync): void
    {
        if ($definition->school_assessment_id === null) {
            $attemptSync->forceFill([
                'sync_status' => 'grade_bridge_skipped',
                'error_message' => 'school_assessment_id is required for grade sync',
            ])->save();

            return;
        }

        $participant = $attemptSync->examParticipant;
        $studentId = $this->resolveSchoolStudentId($participant);

        if ($studentId === null) {
            $attemptSync->forceFill([
                'sync_status' => 'grade_bridge_skipped',
                'error_message' => 'Participant is not linked to a school student',
            ])->save();

            return;
        }

        StudentGrade::query()->updateOrCreate(
            [
                'student_id' => $studentId,
                'assessment_id' => $definition->school_assessment_id,
            ],
            [
                'tenant_id' => $definition->tenant_id,
                'score' => $attemptSync->score,
                'final_score' => $attemptSync->score,
                'is_passed' => $definition->passing_score !== null
                    ? (float) $attemptSync->score >= (float) $definition->passing_score
                    : null,
            ],
        );

        $attemptSync->forceFill([
            'sync_status' => 'grade_synced',
            'synced_at' => now(),
        ])->save();
    }

    public function exportResult(ExamDefinition $definition, ExamResult $result): GradebookExportOutcome
    {
        if (! $this->isGradebookAvailable()) {
            return GradebookExportOutcome::skipped('School gradebook tables are not available.');
        }

        if ($definition->exam_academic_context !== ExamAcademicContext::School) {
            return GradebookExportOutcome::skipped('Exam is not in School academic context.');
        }

        if ($definition->school_assessment_id === null) {
            return GradebookExportOutcome::skipped('school_assessment_id is required for School gradebook export.');
        }

        $participant = $result->examParticipant;
        $studentId = $this->resolveSchoolStudentId($participant);

        if ($studentId === null) {
            return GradebookExportOutcome::skipped('Participant is not linked to a school student.');
        }

        $score = (float) ($result->score ?? 0);
        $passing = $definition->passing_score;

        $grade = StudentGrade::query()->updateOrCreate(
            [
                'student_id' => $studentId,
                'assessment_id' => $definition->school_assessment_id,
            ],
            [
                'tenant_id' => $definition->tenant_id,
                'score' => $score,
                'final_score' => $score,
                'score_letter' => $result->grade_letter,
                'is_passed' => $result->is_passed ?? ($passing !== null ? $score >= (float) $passing : null),
                'graded_at' => $result->submitted_at ?? now(),
            ],
        );

        return GradebookExportOutcome::success(
            StudentGrade::class,
            (int) $grade->getKey(),
            [
                'student_id' => $studentId,
                'assessment_id' => $definition->school_assessment_id,
            ],
        );
    }

    protected function resolveSchoolStudentId(?ExamParticipant $participant): ?int
    {
        if ($participant === null) {
            return null;
        }

        $studentId = $participant->school_student_reference ?? $participant->participant_legacy_id;

        if ($studentId === null && $participant->context_reference_type !== null
            && in_array($participant->context_reference_type, ['student', Student::class], true)) {
            $studentId = $participant->participant_legacy_id;
        }

        return $studentId !== null ? (int) $studentId : null;
    }
}
