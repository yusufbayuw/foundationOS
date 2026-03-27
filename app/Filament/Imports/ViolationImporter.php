<?php

namespace App\Filament\Imports;

use Modules\School\Models\Violation;

class ViolationImporter extends BaseModelImporter
{
    protected static ?string $model = Violation::class;
}
