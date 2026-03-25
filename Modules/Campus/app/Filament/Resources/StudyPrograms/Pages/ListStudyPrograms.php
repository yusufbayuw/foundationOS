<?php

namespace Modules\Campus\Filament\Resources\StudyPrograms\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\StudyPrograms\StudyProgramResource;

class ListStudyPrograms extends ListRecords
{
    protected static string $resource = StudyProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
