<?php

namespace Modules\Exam\Services;

use Illuminate\Support\Facades\Schema;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Campus\Models\StudyResult;
use Modules\Campus\Services\GradebookConfigResolver;
use Modules\Exam\Contracts\GradeBridgeInterface;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\GradeSyncMode;
use Modules\Exam\Models\ExamAttemptSync;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamResult;
use Modules\Exam\Support\GradebookExportOutcome;

class CampusGradeBridgeService implements GradeBridgeInterface
{
    public function __construct(
        protected GradebookConfigResolver $gradebookConfig,
    ) {}

    public function isGradebookAvailable(): bool
    {
        return class_exists(StudyResult::class)
            && Schema::hasTable('study_results');
    }

    public function supports(ExamDefinition $definition): bool
    {
        return $definition->exam_academic_context === ExamAcademicContext::Campus
            && $definition->grade_sync_mode === GradeSyncMode::PushToStudyResult;
    }

    public function syncAttempt(ExamDefinition $definition, ExamAttemptSync $attemptSync): void
    {
        $participant = $attemptSync->examParticipant;
        $studyPlanItemId = $this->resolveStudyPlanItemId($definition, $participant);

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

    public function exportResult(ExamDefinition $definition, ExamResult $result): GradebookExportOutcome
    {
        if (! $this->isGradebookAvailable()) {
            return GradebookExportOutcome::skipped('Campus gradebook tables are not available.');
        }

        if ($definition->exam_academic_context !== ExamAcademicContext::Campus) {
            return GradebookExportOutcome::skipped('Exam is not in Campus academic context.');
        }

        $participant = $result->examParticipant;
        $studyPlanItemId = $this->resolveStudyPlanItemId($definition, $participant);

        if ($studyPlanItemId === null) {
            return GradebookExportOutcome::skipped('Participant is not linked to a study plan item.');
        }

        $componentKey = $definition->grade_sync_target ?? 'final';
        $score = (float) ($result->score ?? 0);
        $percentage = (float) ($result->percentage ?? ($definition->max_score > 0
            ? round(($score / (float) $definition->max_score) * 100, 2)
            : 0));

        $graded = $this->gradebookConfig->gradeFor($definition->tenant_id, $percentage);

        $studyResult = StudyResult::query()->firstOrCreate(
            ['study_plan_item_id' => $studyPlanItemId],
            ['tenant_id' => $definition->tenant_id],
        );

        $components = $studyResult->components_breakdown ?? [];
        $components[$componentKey] = $score;

        $studyResult->forceFill([
            'components_breakdown' => $components,
            'weight_score' => $score,
            'grade_letter' => $result->grade_letter ?? $graded['letter'],
            'grade_point' => $graded['point'],
            'passed' => $result->is_passed ?? ($percentage >= 55),
            'published_at' => $result->submitted_at ?? now(),
            'source' => 'exam_module',
            'notes' => 'Imported from Exam module result '.$result->id,
        ])->save();

        return GradebookExportOutcome::success(
            StudyResult::class,
            (int) $studyResult->getKey(),
            [
                'study_plan_item_id' => $studyPlanItemId,
                'component_key' => $componentKey,
            ],
        );
    }

    protected function resolveStudyPlanItemId(ExamDefinition $definition, ?ExamParticipant $participant): ?int
    {
        if ($participant === null) {
            return null;
        }

        $metadataItemId = $participant->metadata_json['study_plan_item_id'] ?? null;
        if (is_numeric($metadataItemId)) {
            return (int) $metadataItemId;
        }

        if ($participant->context_reference_type !== null
            && in_array($participant->context_reference_type, ['study_plan_item', StudyPlanItem::class], true)) {
            $legacyId = $participant->participant_legacy_id;

            return $legacyId !== null ? (int) $legacyId : null;
        }

        if ($definition->campus_class_reference === null || $participant->campus_student_reference === null) {
            return null;
        }

        $item = StudyPlanItem::withoutTenantScope()
            ->where('tenant_id', $definition->tenant_id)
            ->where('course_offering_id', $definition->campus_class_reference)
            ->whereHas('studyPlan', fn ($query) => $query->where(
                'collage_student_id',
                $participant->campus_student_reference,
            ))
            ->first();

        return $item !== null ? (int) $item->getKey() : null;
    }
}
