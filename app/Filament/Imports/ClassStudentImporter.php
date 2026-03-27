<?php

namespace App\Filament\Imports;

use Modules\School\Models\ClassStudent;

class ClassStudentImporter extends BaseModelImporter
{
    protected static ?string $model = ClassStudent::class;
}
