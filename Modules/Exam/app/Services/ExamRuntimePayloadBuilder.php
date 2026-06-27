<?php

namespace Modules\Exam\Services;

use App\Support\TypedValue;
use Modules\Core\Models\User;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Support\ExamRuntimeEntityRef;

class ExamRuntimePayloadBuilder
{
    public function __construct(
        protected ExamAcademicContextService $academicContextService,
        protected ExamDefinitionScoreCalculator $scoreCalculator,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function buildFull(ExamDefinition $definition): array
    {
        $definition->loadMissing([
            'tenant',
            'owner',
            'schoolAssessment',
            'examDefinitionQuestions.examQuestion.examQuestionOptions',
            'examParticipants.activeToken',
        ]);

        [$questions, $options] = $this->mapQuestionsAndOptions($definition);

        return [
            'foundation_id' => $definition->id,
            'runtime_id' => $this->normalizeRuntimeId($definition->runtime_exam_id),
            'tenant' => ExamRuntimeEntityRef::forTenant($definition->tenant),
            'exam' => $this->mapExam($definition),
            'academic_context' => $this->academicContextService->build($definition),
            'questions' => $questions,
            'options' => $options,
            'participants' => $this->mapParticipants($definition),
            'admin_access' => $this->mapAdminAccess($definition),
            'settings' => $this->mapSettings($definition),
            'metadata' => [
                'grade_sync_mode' => $definition->grade_sync_mode->value,
                'grade_sync_target' => $definition->grade_sync_target,
                'school_assessment_reference' => ExamRuntimeEntityRef::forModel($definition->schoolAssessment),
                'snapshot_version' => TypedValue::int($definition->examPublishSnapshots()->max('version')) + 1,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function buildParticipantsOnly(ExamDefinition $definition): array
    {
        $definition->loadMissing(['tenant', 'examParticipants.activeToken']);

        return [
            'foundation_id' => $definition->id,
            'runtime_id' => $this->normalizeRuntimeId($definition->runtime_exam_id),
            'tenant' => ExamRuntimeEntityRef::forTenant($definition->tenant),
            'participants' => $this->mapParticipants($definition),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function buildAdminAccessOnly(ExamDefinition $definition): array
    {
        $definition->loadMissing(['tenant', 'owner']);

        return [
            'foundation_id' => $definition->id,
            'runtime_id' => $this->normalizeRuntimeId($definition->runtime_exam_id),
            'tenant' => ExamRuntimeEntityRef::forTenant($definition->tenant),
            'admin_access' => $this->mapAdminAccess($definition),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapExam(ExamDefinition $definition): array
    {
        return [
            'foundation_id' => $definition->id,
            'title' => $definition->name,
            'description' => $definition->description,
            'code' => $definition->code,
            'exam_type' => $definition->exam_type?->value,
            'exam_purpose' => $definition->exam_purpose?->value,
            'status' => $definition->status->value,
            'max_score' => $definition->max_score ?? $this->scoreCalculator->totalScore($definition),
            'passing_score' => $definition->passing_score,
            'duration_minutes' => $definition->duration_minutes,
            'max_attempts' => $definition->max_attempts,
            'starts_at' => $definition->starts_at?->toIso8601String(),
            'ends_at' => $definition->ends_at?->toIso8601String(),
            'published_at' => $definition->published_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapSettings(ExamDefinition $definition): array
    {
        return [
            'shuffle_questions' => $definition->shuffle_questions,
            'shuffle_options' => $definition->shuffle_options,
            'show_result' => $definition->show_result,
            'show_explanation' => $definition->show_explanation,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function mapParticipants(ExamDefinition $definition): array
    {
        return $definition->examParticipants
            ->map(fn (ExamParticipant $participant): array => [
                'foundation_id' => $participant->id,
                'student_name' => $participant->student_name,
                'student_identifier' => $participant->student_identifier,
                'email' => $participant->email,
                'status' => $participant->status->value,
                'participant_source' => $participant->participant_source?->value,
                'token' => $participant->activeToken?->token,
                'assigned_at' => $participant->assigned_at?->toIso8601String(),
                'school_student_reference' => $participant->school_student_reference !== null
                    ? ['reference_type' => 'Modules\\School\\Models\\Student', 'reference_id' => $participant->school_student_reference]
                    : null,
                'campus_student_reference' => $participant->campus_student_reference !== null
                    ? ['reference_type' => 'Modules\\Campus\\Models\\CollageStudent', 'reference_id' => $participant->campus_student_reference]
                    : null,
                'user_reference' => $participant->user_reference !== null
                    ? ['reference_type' => 'Modules\\Core\\Models\\User', 'reference_id' => $participant->user_reference]
                    : null,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function mapAdminAccess(ExamDefinition $definition): array
    {
        $access = [];
        $seen = [];

        if ($definition->owner_user_id !== null) {
            $access[] = $this->mapAdminUser($definition->owner, 'owner');
            $seen[$definition->owner_user_id] = true;
        }

        $metadata = $definition->metadata_json ?? [];
        $proctorUserIds = is_array($metadata['proctor_user_ids'] ?? null) ? $metadata['proctor_user_ids'] : [];
        $adminAccessRows = is_array($metadata['admin_access'] ?? null) ? $metadata['admin_access'] : [];

        foreach ($proctorUserIds as $userId) {
            $userId = TypedValue::int($userId);

            if (isset($seen[$userId])) {
                continue;
            }

            $user = User::query()->find($userId);
            $access[] = $this->mapAdminUser($user, 'proctor');
            $seen[$userId] = true;
        }

        foreach ($adminAccessRows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $userId = TypedValue::int($row['user_id'] ?? $row['user_reference'] ?? 0);

            if ($userId === 0 || isset($seen[$userId])) {
                continue;
            }

            $user = User::query()->find($userId);
            $access[] = $this->mapAdminUser($user, TypedValue::string($row['role'] ?? 'proctor'));
            $seen[$userId] = true;
        }

        return $access;
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapAdminUser(?User $user, string $role): array
    {
        return [
            'role' => $role,
            'name' => $user?->name,
            'email' => $user?->email,
            'user_reference' => $user !== null
                ? ['reference_type' => User::class, 'reference_id' => (int) $user->id]
                : null,
        ];
    }

    /**
     * @return array{0: list<array<string, mixed>>, 1: list<array<string, mixed>>}
     */
    protected function mapQuestionsAndOptions(ExamDefinition $definition): array
    {
        $questions = [];
        $options = [];

        foreach ($definition->examDefinitionQuestions->sortBy('sort_order') as $item) {
            $question = $item->examQuestion;

            if ($question === null) {
                continue;
            }

            $questions[] = [
                'foundation_id' => $question->id,
                'pivot_foundation_id' => $item->id,
                'sort_order' => $item->sort_order,
                'score' => $item->effectiveScore(),
                'type' => $question->type->value,
                'topic' => $question->topic,
                'subtopic' => $question->subtopic,
                'difficulty' => $question->difficulty?->value,
                'question_text' => $question->question_text,
                'correct_answer' => $question->correct_answer,
                'explanation' => $question->explanation,
            ];

            foreach ($question->examQuestionOptions as $option) {
                $options[] = [
                    'foundation_id' => $option->id,
                    'question_foundation_id' => $question->id,
                    'option_text' => $option->option_text,
                    'is_correct' => $option->is_correct,
                    'sort_order' => $option->sort_order,
                ];
            }
        }

        return [$questions, $options];
    }

    protected function normalizeRuntimeId(?string $runtimeId): ?string
    {
        if ($runtimeId === null || $runtimeId === '') {
            return null;
        }

        return $runtimeId;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function summarizeForLog(array $payload): array
    {
        return [
            'foundation_id' => $payload['foundation_id'] ?? null,
            'runtime_id' => $payload['runtime_id'] ?? null,
            'question_count' => $this->countList($payload['questions'] ?? []),
            'option_count' => $this->countList($payload['options'] ?? []),
            'participant_count' => $this->countList($payload['participants'] ?? []),
            'admin_access_count' => $this->countList($payload['admin_access'] ?? []),
        ];
    }

    private function countList(mixed $value): int
    {
        return is_countable($value) ? count($value) : 0;
    }
}
