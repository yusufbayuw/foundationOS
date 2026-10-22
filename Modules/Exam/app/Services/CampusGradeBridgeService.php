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

        if (StudyResult::withoutTenantScope()
            ->where('study_plan_item_id', $studyPlanItemId)
            ->where('tenant_id', '!=', $definition->tenant_id)
            ->exists()) {
            $attemptSync->forceFill([
                'sync_status' => 'grade_bridge_skipped',
                'error_message' => 'Existing study result belongs to another tenant',
            ])->save();

            return;
        }

        $studyResult = StudyResult::query()->firstOrCreate(
            ['tenant_id' => $definition->tenant_id, 'study_plan_item_id' => $studyPlanItemId],
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

        if (StudyResult::withoutTenantScope()
            ->where('study_plan_item_id', $studyPlanItemId)
            ->where('tenant_id', '!=', $definition->tenant_id)
            ->exists()) {
            return GradebookExportOutcome::skipped('An existing grade result belongs to another tenant.');
        }

        $studyResult = StudyResult::query()->firstOrCreate(
            ['tenant_id' => $definition->tenant_id, 'study_plan_item_id' => $studyPlanItemId],
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
        if ($participant === null
            || (int) $participant->tenant_id !== (int) $definition->tenant_id
            || (string) $participant->exam_definition_id !== (string) $definition->getKey()
            || $participant->campus_student_reference === null) {
            return null;
        }

        $metadataItemId = $participant->metadata_json['study_plan_item_id'] ?? null;
        $candidateId = is_numeric($metadataItemId) && (int) $metadataItemId > 0
            ? (int) $metadataItemId
            : null;

        if ($candidateId === null
            && in_array($participant->context_reference_type, ['study_plan_item', StudyPlanItem::class], true)
            && $participant->participant_legacy_id !== null) {
            $candidateId = (int) $participant->participant_legacy_id;
        }

        if ($candidateId === null && $definition->campus_class_reference === null) {
            return null;
        }

        // Metadata and legacy references are untrusted: validate student,
        // offering, course and tenant before any gradebook write.
        $item = StudyPlanItem::withoutTenantScope()
            ->where('tenant_id', $definition->tenant_id)
            ->when($candidateId !== null, fn ($query) => $query->whereKey($candidateId))
            ->when($definition->campus_class_reference !== null, fn ($query) => $query
                ->where('course_offering_id', $definition->campus_class_reference))
            ->when($definition->campus_course_reference !== null, fn ($query) => $query
                ->where('course_id', $definition->campus_course_reference))
            ->whereHas('studyPlan', fn ($query) => $query
                ->where('tenant_id', $definition->tenant_id)
                ->where('collage_student_id', $participant->campus_student_reference))
            ->first();

        return $item !== null ? (int) $item->getKey() : null;
    }
}
