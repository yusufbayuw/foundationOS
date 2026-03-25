<?php

namespace Modules\School\Filament\Resources\Teachers\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\Teachers\TeacherResource;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;
}
