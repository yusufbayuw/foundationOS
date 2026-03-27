<?php

namespace App\Filament\Imports;

use Modules\Employee\Models\Position;

class PositionImporter extends BaseModelImporter
{
    protected static ?string $model = Position::class;
}
