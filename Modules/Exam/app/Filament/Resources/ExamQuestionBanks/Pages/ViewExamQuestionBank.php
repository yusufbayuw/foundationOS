<?php

namespace Modules\Exam\Filament\Resources\ExamQuestionBanks\Pages;

use App\Filament\Imports\ExamQuestionBulkImporter;
use Filament\Resources\Pages\ViewRecord;
use Modules\Exam\Filament\Resources\ExamQuestionBanks\ExamQuestionBankResource;
use Modules\Exam\Filament\Support\ExamQuestionImportTableActions;

class ViewExamQuestionBank extends ViewRecord
{
    protected static string $resource = ExamQuestionBankResource::class;

    protected function getHeaderActions(): array
    {
        return ExamQuestionImportTableActions::make(
            ExamQuestionBulkImporter::class,
            (string) $this->getRecord()->getKey(),
        );
    }
}
