<?php

namespace App\Filament\Imports;

use Modules\School\Models\Subject;

class SubjectImporter extends BaseModelImporter
{
    protected static ?string $model = Subject::class;
}
