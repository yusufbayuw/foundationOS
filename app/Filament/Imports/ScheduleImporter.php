<?php

namespace App\Filament\Imports;

use Modules\School\Models\Schedule;

class ScheduleImporter extends BaseModelImporter
{
    protected static ?string $model = Schedule::class;
}
