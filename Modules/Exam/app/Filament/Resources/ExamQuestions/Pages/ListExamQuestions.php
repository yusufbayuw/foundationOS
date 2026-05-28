<?php

namespace Modules\Exam\Filament\Resources\ExamQuestions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Exam\Filament\Resources\ExamQuestions\ExamQuestionResource;

class ListExamQuestions extends ListRecords
{
    protected static string $resource = ExamQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
