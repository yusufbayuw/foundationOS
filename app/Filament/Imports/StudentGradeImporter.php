<?php

namespace App\Filament\Imports;

use Modules\School\Models\StudentGrade;

class StudentGradeImporter extends BaseModelImporter
{
    protected static ?string $model = StudentGrade::class;
}
