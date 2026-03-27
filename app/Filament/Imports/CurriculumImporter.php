<?php

namespace App\Filament\Imports;

use Modules\School\Models\Curriculum;

class CurriculumImporter extends BaseModelImporter
{
    protected static ?string $model = Curriculum::class;
}
