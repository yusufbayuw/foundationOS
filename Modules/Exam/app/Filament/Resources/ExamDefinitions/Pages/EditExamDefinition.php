<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Exam\Filament\Resources\ExamDefinitions\ExamDefinitionResource;

class EditExamDefinition extends EditRecord
{
    protected static string $resource = ExamDefinitionResource::class;
}
