<?php

namespace Modules\Campus\Filament\Resources\StudyPrograms\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\StudyPrograms\StudyProgramResource;

class ViewStudyProgram extends ViewRecord
{
    protected static string $resource = StudyProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
