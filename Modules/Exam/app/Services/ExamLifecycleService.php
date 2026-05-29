<?php

namespace Modules\Exam\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamDefinitionQuestion;

class ExamLifecycleService
{
    public function __construct(
        protected ExamDefinitionScoreCalculator $scoreCalculator,
    ) {}

    public function markReady(ExamDefinition $definition): ExamDefinition
    {
        if ($definition->status !== ExamStatus::Draft) {
            throw new \InvalidArgumentException('Only draft exams can be marked as ready.');
        }

        if ($this->scoreCalculator->questionCount($definition) < 1) {
            throw new \InvalidArgumentException('Add at least one question before marking the exam as ready.');
        }

        if ($definition->starts_at !== null && $definition->ends_at !== null && $definition->ends_at->lte($definition->starts_at)) {
            throw new \InvalidArgumentException('End time must be after start time.');
        }

        $definition->forceFill(['status' => ExamStatus::Ready])->save();

        return $definition->refresh();
    }

    public function close(ExamDefinition $definition): ExamDefinition
    {
        if (! in_array($definition->status, [ExamStatus::Ready, ExamStatus::Published, ExamStatus::Scheduled], true)) {
            throw new \InvalidArgumentException('This exam cannot be closed from its current status.');
        }

        $definition->forceFill(['status' => ExamStatus::Closed])->save();

        return $definition->refresh();
    }

    public function duplicate(ExamDefinition $definition): ExamDefinition
    {
        return DB::transaction(function () use ($definition): ExamDefinition {
            $definition->loadMissing(['examDefinitionQuestions']);

            $copy = $definition->replicate([
                'runtime_exam_id',
                'published_at',
                'last_published_at',
            ]);

            $copy->id = (string) Str::uuid();
            $copy->status = ExamStatus::Draft;
            $copy->code = $definition->code
                ? $definition->code.'-copy-'.Str::lower(Str::random(4))
                : null;
            $copy->name = $definition->name.' (Copy)';
            $copy->save();

            foreach ($definition->examDefinitionQuestions as $item) {
                ExamDefinitionQuestion::query()->create([
                    'id' => (string) Str::uuid(),
                    'tenant_id' => $item->tenant_id,
                    'exam_definition_id' => $copy->id,
                    'exam_question_id' => $item->exam_question_id,
                    'sort_order' => $item->sort_order,
                    'score_override' => $item->score_override,
                ]);
            }

            return $copy->refresh();
        });
    }
}
