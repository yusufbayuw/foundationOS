<?php

namespace Modules\Campus\Filament\Resources\Faculties\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\Faculties\FacultyResource;

class ViewFaculty extends ViewRecord
{
    protected static string $resource = FacultyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
