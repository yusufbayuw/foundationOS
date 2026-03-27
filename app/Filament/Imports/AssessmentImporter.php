<?php

namespace App\Filament\Imports;

use Modules\School\Models\Assessment;

class AssessmentImporter extends BaseModelImporter
{
    protected static ?string $model = Assessment::class;
}
