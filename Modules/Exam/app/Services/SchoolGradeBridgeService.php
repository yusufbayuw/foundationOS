<?php

namespace Modules\Exam\Services;

use Modules\Exam\Contracts\GradeBridgeInterface;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\GradeSyncMode;
use Modules\Exam\Models\ExamAttemptSync;
use Modules\Exam\Models\ExamDefinition;
use Modules\School\Models\Student;
use Modules\School\Models\StudentGrade;

class SchoolGradeBridgeService implements GradeBridgeInterface
{
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
        $studentId = $participant?->context_reference_id;

        if ($participant?->context_reference_type !== null
            && ! in_array($participant->context_reference_type, ['student', Student::class], true)) {
            $studentId = null;
        }

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
}
