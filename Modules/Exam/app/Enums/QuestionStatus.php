<?php

namespace Modules\Exam\Enums;

enum QuestionStatus: string
{
    case Active = 'active';
    case Draft = 'draft';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Draft => 'Draft',
            self::Archived => 'Archived',
        };
    }
}
