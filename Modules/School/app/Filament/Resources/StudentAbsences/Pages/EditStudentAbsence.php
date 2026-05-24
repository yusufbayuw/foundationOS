<?php

namespace Modules\School\Filament\Resources\StudentAbsences\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\School\Filament\Resources\StudentAbsences\StudentAbsenceResource;

class EditStudentAbsence extends EditRecord
{
    protected static string $resource = StudentAbsenceResource::class;
}
