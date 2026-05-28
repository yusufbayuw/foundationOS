<?php

namespace Modules\Exam\Services;

use Modules\Campus\Models\StudyPlanItem;
use Modules\Campus\Models\StudyResult;
use Modules\Exam\Contracts\GradeBridgeInterface;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\GradeSyncMode;
use Modules\Exam\Models\ExamAttemptSync;
use Modules\Exam\Models\ExamDefinition;

class CampusGradeBridgeService implements GradeBridgeInterface
{
    public function supports(ExamDefinition $definition): bool
    {
        return $definition->exam_academic_context === ExamAcademicContext::Campus
            && $definition->grade_sync_mode === GradeSyncMode::PushToStudyResult;
    }

    public function syncAttempt(ExamDefinition $definition, ExamAttemptSync $attemptSync): void
    {
        $participant = $attemptSync->examParticipant;
        $studyPlanItemId = $participant?->context_reference_id;

        if ($participant?->context_reference_type !== null
            && ! in_array($participant->context_reference_type, ['study_plan_item', StudyPlanItem::class], true)) {
            $studyPlanItemId = null;
        }

        if ($studyPlanItemId === null) {
            $attemptSync->forceFill([
                'sync_status' => 'grade_bridge_skipped',
                'error_message' => 'Participant is not linked to a study plan item',
            ])->save();

            return;
        }

        $componentKey = $definition->grade_sync_target ?? 'final';

        $studyResult = StudyResult::query()->firstOrCreate(
            ['study_plan_item_id' => $studyPlanItemId],
            ['tenant_id' => $definition->tenant_id],
        );

        $components = $studyResult->components_breakdown ?? [];
        $components[$componentKey] = $attemptSync->score;

        $studyResult->forceFill([
            'components_breakdown' => $components,
            'weight_score' => $attemptSync->score,
            'source' => 'exam_module',
        ])->save();

        $attemptSync->forceFill([
            'sync_status' => 'grade_synced',
            'synced_at' => now(),
        ])->save();
    }
}
