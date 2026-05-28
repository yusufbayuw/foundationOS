<?php

namespace Modules\Exam\Enums;

enum QuestionType: string
{
    case SingleChoice = 'single_choice';
    case TrueFalse = 'true_false';
    case MultipleChoice = 'multiple_choice';
    case ShortAnswer = 'short_answer';
    case Numeric = 'numeric';
    case Essay = 'essay';

    public function label(): string
    {
        return match ($this) {
            self::SingleChoice => 'Single choice',
            self::TrueFalse => 'True false',
            self::MultipleChoice => 'Multiple choice',
            self::ShortAnswer => 'Short answer',
            self::Numeric => 'Numeric',
            self::Essay => 'Essay',
        };
    }

    public function supportsOptions(): bool
    {
        return in_array($this, [
            self::SingleChoice,
            self::TrueFalse,
            self::MultipleChoice,
        ], true);
    }
}
