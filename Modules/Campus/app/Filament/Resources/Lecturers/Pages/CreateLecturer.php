<?php

namespace Modules\Campus\Filament\Resources\Lecturers\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Campus\Filament\Resources\Lecturers\LecturerResource;

class CreateLecturer extends CreateRecord
{
    protected static string $resource = LecturerResource::class;
}
