<?php

namespace Modules\Exam\Filament\Resources\ExamDefinitions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Exam\Filament\Resources\ExamDefinitions\ExamDefinitionResource;

class ListExamDefinitions extends ListRecords
{
    protected static string $resource = ExamDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
