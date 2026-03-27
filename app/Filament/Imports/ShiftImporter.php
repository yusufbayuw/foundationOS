<?php

namespace App\Filament\Imports;

use Modules\Employee\Models\Shift;

class ShiftImporter extends BaseModelImporter
{
    protected static ?string $model = Shift::class;
}
