<?php

namespace Modules\Exam\Enums;

enum ExamStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Published = 'published';
    case Closed = 'closed';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Scheduled => 'Scheduled',
            self::Published => 'Published',
            self::Closed => 'Closed',
            self::Archived => 'Archived',
        };
    }
}
