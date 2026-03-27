<?php

namespace App\Filament\Imports;

use Modules\Employee\Models\AttendanceLog;

class AttendanceLogImporter extends BaseModelImporter
{
    protected static ?string $model = AttendanceLog::class;
}
