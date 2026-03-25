<?php

namespace Modules\School\Filament\Resources\Attendances\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\Attendances\AttendanceResource;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;
}
