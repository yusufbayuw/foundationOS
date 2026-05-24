<?php

namespace Modules\School\Filament\Resources\StudentAbsences\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\School\Filament\Resources\StudentAbsences\StudentAbsenceResource;

class ListStudentAbsences extends ListRecords
{
    protected static string $resource = StudentAbsenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
