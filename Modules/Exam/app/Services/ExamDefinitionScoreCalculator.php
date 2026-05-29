<?php

namespace Modules\Exam\Services;

use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamDefinitionQuestion;

class ExamDefinitionScoreCalculator
{
    public function questionCount(ExamDefinition $definition): int
    {
        return $definition->examDefinitionQuestions()->count();
    }

    public function totalScore(ExamDefinition $definition): float
    {
        $definition->loadMissing(['examDefinitionQuestions.examQuestion']);

        return (float) $definition->examDefinitionQuestions->sum(
            fn (ExamDefinitionQuestion $item): float => $item->effectiveScore(),
        );
    }
}
