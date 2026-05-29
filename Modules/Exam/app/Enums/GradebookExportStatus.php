<?php

namespace Modules\Exam\Enums;

enum GradebookExportStatus: string
{
    case Success = 'success';
    case Skipped = 'skipped';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Success => 'Success',
            self::Skipped => 'Skipped',
            self::Failed => 'Failed',
        };
    }
}
