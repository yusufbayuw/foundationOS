<?php

namespace Modules\School\Filament\Resources\StudentAbsences\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\School\Filament\Resources\StudentAbsences\StudentAbsenceResource;

class ViewStudentAbsence extends ViewRecord
{
    protected static string $resource = StudentAbsenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
