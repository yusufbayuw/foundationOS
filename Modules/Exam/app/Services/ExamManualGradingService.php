<?php

namespace Modules\Exam\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAuditAction;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Models\ExamAnswer;
use Modules\Exam\Models\ExamAttempt;
use Modules\Exam\Models\ExamResult;

class ExamManualGradingService
{
    public function __construct(
        protected ExamAuditLogger $auditLogger,
        protected ExamGradebookEventDispatcher $gradebookEvents,
    ) {}

    public function gradeEssay(
        ExamAnswer $answer,
        User $grader,
        float $manualScore,
        ?string $feedback = null,
        ?array $rubric = null,
    ): ExamAnswer {
        if ($answer->examQuestion?->type !== QuestionType::Essay) {
            throw new \InvalidArgumentException('Only essay answers can be manually graded.');
        }

        return DB::transaction(function () use ($answer, $grader, $manualScore, $feedback, $rubric): ExamAnswer {
            $answer->forceFill([
                'manual_score' => $manualScore,
                'score' => $manualScore,
                'feedback' => $feedback,
                'rubric_json' => $rubric,
                'graded_by' => $grader->id,
                'graded_at' => now(),
            ])->save();

            $this->recalculateAttemptScore($answer->examAttempt);
            $this->recalculateResult($answer->examAttempt);

            $graded = $answer->refresh();

            $definition = $answer->examDefinition;

            if ($definition !== null) {
                $this->auditLogger->log(
                    ExamAuditAction::GradeAnswer,
                    $definition,
                    'Essay answer manually graded.',
                    $grader,
                    $graded,
                    newValues: [
                        'answer_id' => $graded->id,
                        'manual_score' => $graded->manual_score,
                    ],
                );
            }

            return $graded;
        });
    }

    protected function recalculateAttemptScore(?ExamAttempt $attempt): void
    {
        if ($attempt === null) {
            return;
        }

        $total = $attempt->examAnswers()
            ->get()
            ->sum(fn (ExamAnswer $answer): float => $answer->effectiveScore() ?? 0.0);

        $attempt->forceFill(['score' => $total])->save();
    }

    protected function recalculateResult(?ExamAttempt $attempt): void
    {
        if ($attempt === null) {
            return;
        }

        $definition = $attempt->examDefinition;
        $maxScore = (float) ($definition?->max_score ?? 0);
        $score = (float) ($attempt->score ?? 0);
        $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : null;
        $passing = $definition?->passing_score;

        $updated = ExamResult::withoutTenantScope()
            ->where('exam_attempt_id', $attempt->id)
            ->update([
                'score' => $score,
                'percentage' => $percentage,
                'is_passed' => $passing !== null ? $score >= (float) $passing : null,
            ]);

        if ($updated > 0) {
            ExamResult::withoutTenantScope()
                ->where('exam_attempt_id', $attempt->id)
                ->whereNotNull('exam_participant_id')
                ->each(fn (ExamResult $result): mixed => $this->gradebookEvents->graded($result));
        }
    }
}
