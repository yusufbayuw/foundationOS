<?php

namespace Modules\Exam\Services;

use Modules\Exam\Events\ExamResultGraded;
use Modules\Exam\Events\ExamResultPublished;
use Modules\Exam\Events\ExamResultSynced;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamResult;

class ExamGradebookEventDispatcher
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function published(ExamDefinition $definition, array $context = []): void
    {
        event(new ExamResultPublished(
            examId: (string) $definition->id,
            context: array_merge($this->baseContext($definition), $context),
        ));
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function synced(ExamResult $result, array $context = []): void
    {
        $result->loadMissing('examDefinition', 'examParticipant');

        event(new ExamResultSynced(
            examId: (string) $result->exam_definition_id,
            resultId: (string) $result->id,
            participantId: (string) $result->exam_participant_id,
            context: array_merge(
                $this->baseContext($result->examDefinition),
                $this->resultContext($result),
                $context,
            ),
        ));
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function graded(ExamResult $result, array $context = []): void
    {
        $result->loadMissing('examDefinition', 'examParticipant');

        event(new ExamResultGraded(
            examId: (string) $result->exam_definition_id,
            resultId: (string) $result->id,
            participantId: (string) $result->exam_participant_id,
            context: array_merge(
                $this->baseContext($result->examDefinition),
                $this->resultContext($result),
                $context,
            ),
        ));
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseContext(?ExamDefinition $definition): array
    {
        if ($definition === null) {
            return [];
        }

        return [
            'tenant_id' => $definition->tenant_id,
            'exam_academic_context' => $definition->exam_academic_context->value,
            'school_assessment_id' => $definition->school_assessment_id,
            'grade_sync_mode' => $definition->grade_sync_mode->value,
            'grade_sync_target' => $definition->grade_sync_target,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function resultContext(ExamResult $result): array
    {
        return [
            'score' => $result->score,
            'max_score' => $result->max_score,
            'percentage' => $result->percentage,
            'grade_letter' => $result->grade_letter,
            'is_passed' => $result->is_passed,
            'status' => $result->status,
        ];
    }
}
