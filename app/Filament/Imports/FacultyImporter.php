<?php

namespace App\Filament\Imports;

use Modules\Campus\Models\Faculty;

class FacultyImporter extends BaseModelImporter
{
    protected static ?string $model = Faculty::class;
}
