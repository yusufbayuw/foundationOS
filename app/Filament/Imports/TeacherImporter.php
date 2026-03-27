<?php

namespace App\Filament\Imports;

use Modules\School\Models\Teacher;

class TeacherImporter extends BaseModelImporter
{
    protected static ?string $model = Teacher::class;
}
