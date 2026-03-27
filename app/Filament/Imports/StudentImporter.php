<?php

namespace App\Filament\Imports;

use Modules\School\Models\Student;

class StudentImporter extends BaseModelImporter
{
    protected static ?string $model = Student::class;
}
