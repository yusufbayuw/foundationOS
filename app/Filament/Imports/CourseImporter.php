<?php

namespace App\Filament\Imports;

use Modules\Campus\Models\Course;

class CourseImporter extends BaseModelImporter
{
    protected static ?string $model = Course::class;
}
