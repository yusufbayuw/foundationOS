<?php

namespace Modules\Exam\Services;

use Illuminate\Support\Facades\DB;
use Modules\Exam\Contracts\RuntimePublisherInterface;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamPublishSnapshot;

class ExamPublishService
{
    public function __construct(
        protected ExamContextResolver $contextResolver,
        protected ?RuntimePublisherInterface $runtimePublisher = null,
    ) {}

    public function createSnapshot(ExamDefinition $definition): ExamPublishSnapshot
    {
        $version = (int) $definition->examPublishSnapshots()->max('version') + 1;

        return ExamPublishSnapshot::query()->create([
            'tenant_id' => $definition->tenant_id,
            'exam_definition_id' => $definition->id,
            'version' => $version,
            'payload_json' => $this->buildPayload($definition),
            'publish_status' => 'pending',
        ]);
    }

    public function publish(ExamDefinition $definition): ExamPublishSnapshot
    {
        return DB::transaction(function () use ($definition): ExamPublishSnapshot {
            $snapshot = $this->createSnapshot($definition);

            if ($this->runtimePublisher !== null) {
                $result = $this->runtimePublisher->publish($definition, $snapshot);

                $snapshot->forceFill([
                    'runtime_exam_id' => $result['runtime_exam_id'] ?? null,
                    'publish_status' => $result['publish_status'] ?? 'published',
                    'published_at' => now(),
                ])->save();
            } else {
                $snapshot->forceFill([
                    'publish_status' => 'stubbed',
                    'published_at' => now(),
                ])->save();
            }

            $definition->forceFill([
                'status' => ExamStatus::Published,
                'published_at' => $definition->published_at ?? now(),
                'last_published_at' => now(),
                'runtime_exam_id' => $snapshot->runtime_exam_id ?? $definition->runtime_exam_id,
            ])->save();

            return $snapshot->refresh();
        });
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildPayload(ExamDefinition $definition): array
    {
        $definition->loadMissing([
            'tenant',
            'examPackages.examQuestionBank.examQuestions.examQuestionOptions',
            'organization',
            'academicPeriod',
        ]);

        return [
            'exam_definition_id' => $definition->id,
            'tenant_uuid' => $definition->tenant?->uuid,
            'tenant_id' => $definition->tenant_id,
            'organization_id' => $definition->organization_id,
            'exam_academic_context' => $definition->exam_academic_context?->value,
            'exam_purpose' => $definition->exam_purpose?->value,
            'context' => $this->contextResolver->resolveDefinitionContext($definition),
            'settings' => [
                'name' => $definition->name,
                'code' => $definition->code,
                'duration_minutes' => $definition->duration_minutes,
                'max_attempts' => $definition->max_attempts,
                'max_score' => $definition->max_score,
                'passing_score' => $definition->passing_score,
                'shuffle_questions' => $definition->shuffle_questions,
                'starts_at' => $definition->starts_at?->toIso8601String(),
                'ends_at' => $definition->ends_at?->toIso8601String(),
            ],
            'packages' => $definition->examPackages->map(function ($package) {
                $bank = $package->examQuestionBank;

                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'question_bank_id' => $package->exam_question_bank_id,
                    'questions' => $bank?->examQuestions->map(fn ($question) => [
                        'id' => $question->id,
                        'type' => $question->type?->value,
                        'topic' => $question->topic,
                        'question_text' => $question->question_text,
                        'score' => $question->score,
                        'options' => $question->examQuestionOptions->map(fn ($option) => [
                            'id' => $option->id,
                            'option_text' => $option->option_text,
                            'is_correct' => $option->is_correct,
                            'sort_order' => $option->sort_order,
                        ])->values()->all(),
                    ])->values()->all() ?? [],
                ];
            })->values()->all(),
        ];
    }
}
