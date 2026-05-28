<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Exam\Filament\Resources\ExamDefinitions\ExamDefinitionResource;

class CreateExamDefinition extends CreateRecord
{
    protected static string $resource = ExamDefinitionResource::class;
}
