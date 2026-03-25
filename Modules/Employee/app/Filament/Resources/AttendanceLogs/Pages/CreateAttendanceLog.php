<?php

namespace Modules\Employee\Filament\Resources\AttendanceLogs\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Employee\Filament\Resources\AttendanceLogs\AttendanceLogResource;

class CreateAttendanceLog extends CreateRecord
{
    protected static string $resource = AttendanceLogResource::class;
}
