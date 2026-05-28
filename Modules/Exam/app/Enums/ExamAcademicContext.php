<?php

namespace Modules\Exam\Enums;

enum ExamAcademicContext: string
{
    case School = 'school';
    case Campus = 'campus';
    case Standalone = 'standalone';

    public function label(): string
    {
        return match ($this) {
            self::School => 'School',
            self::Campus => 'Campus',
            self::Standalone => 'Standalone',
        };
    }
}
