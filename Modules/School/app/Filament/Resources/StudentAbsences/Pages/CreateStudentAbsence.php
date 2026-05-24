<?php

namespace Modules\School\Filament\Resources\StudentAbsences\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\StudentAbsences\StudentAbsenceResource;

class CreateStudentAbsence extends CreateRecord
{
    protected static string $resource = StudentAbsenceResource::class;
}
