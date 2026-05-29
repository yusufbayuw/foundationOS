<?php

namespace Modules\Exam\Enums;

enum ExamExportType: string
{
    case Csv = 'csv';
    case Excel = 'excel';
    case Pdf = 'pdf';

    public function label(): string
    {
        return match ($this) {
            self::Csv => 'CSV',
            self::Excel => 'Excel',
            self::Pdf => 'PDF',
        };
    }
}
