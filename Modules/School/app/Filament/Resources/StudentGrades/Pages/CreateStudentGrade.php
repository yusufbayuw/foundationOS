<?php

namespace Modules\School\Filament\Resources\StudentGrades\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\StudentGrades\StudentGradeResource;

class CreateStudentGrade extends CreateRecord
{
    protected static string $resource = StudentGradeResource::class;
}
