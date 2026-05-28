<?php

namespace Modules\Exam\Filament\Resources\ExamQuestions\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Exam\Filament\Resources\ExamQuestions\ExamQuestionResource;
use Modules\Exam\Filament\Support\ExamQuestionFormSupport;
use Modules\Exam\Models\ExamQuestion;

class EditExamQuestion extends EditRecord
{
    protected static string $resource = ExamQuestionResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var ExamQuestion $record */
        $record = $this->getRecord();

        return array_merge($data, ExamQuestionFormSupport::hydrateFormData($record));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return ExamQuestionFormSupport::dehydrateFormData($data);
    }
}
