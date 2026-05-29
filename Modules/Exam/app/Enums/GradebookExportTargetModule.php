<?php

namespace Modules\Exam\Enums;

enum GradebookExportTargetModule: string
{
    case School = 'school';
    case Campus = 'campus';

    public function label(): string
    {
        return match ($this) {
            self::School => 'School',
            self::Campus => 'Campus',
        };
    }
}
