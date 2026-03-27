<?php

namespace App\Filament\Imports;

use Modules\School\Models\Attendance;

class AttendanceImporter extends BaseModelImporter
{
    protected static ?string $model = Attendance::class;
}
