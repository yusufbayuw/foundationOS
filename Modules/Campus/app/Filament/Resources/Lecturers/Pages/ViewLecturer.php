<?php

namespace Modules\Campus\Filament\Resources\Lecturers\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\Lecturers\LecturerResource;

class ViewLecturer extends ViewRecord
{
    protected static string $resource = LecturerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
