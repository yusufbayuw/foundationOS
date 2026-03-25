<?php

namespace Modules\School\Filament\Resources\ClassStudents\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\ClassStudents\ClassStudentResource;

class CreateClassStudent extends CreateRecord
{
    protected static string $resource = ClassStudentResource::class;
}
