<?php

namespace Modules\Exam\Filament\Resources\ExamQuestions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Exam\Filament\Resources\ExamQuestions\ExamQuestionResource;
use Modules\Exam\Filament\Support\ExamQuestionFormSupport;

class CreateExamQuestion extends CreateRecord
{
    protected static string $resource = ExamQuestionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return ExamQuestionFormSupport::dehydrateFormData($data);
    }
}
