<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ExamType;
use Modules\Exam\Filament\Resources\ExamDefinitions\ExamDefinitionResource;

class CreateExamDefinition extends CreateRecord
{
    protected static string $resource = ExamDefinitionResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = ExamStatus::Draft->value;

        if (isset($data['exam_type']) && ! isset($data['exam_purpose'])) {
            $examType = ExamType::from($data['exam_type']);
            $data['exam_purpose'] = $examType->toExamPurpose()->value;
        }

        return $data;
    }
}
