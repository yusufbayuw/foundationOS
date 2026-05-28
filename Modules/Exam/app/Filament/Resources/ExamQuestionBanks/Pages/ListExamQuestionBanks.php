<?php

namespace Modules\Exam\Filament\Resources\ExamQuestionBanks\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Exam\Filament\Resources\ExamQuestionBanks\ExamQuestionBankResource;

class ListExamQuestionBanks extends ListRecords
{
    protected static string $resource = ExamQuestionBankResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
