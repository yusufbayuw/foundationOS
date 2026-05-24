<?php

namespace Modules\Boarding\Filament\Resources\BoardingAttendances\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Boarding\Filament\Resources\BoardingAttendances\BoardingAttendanceResource;

class CreateBoardingAttendance extends CreateRecord
{
    protected static string $resource = BoardingAttendanceResource::class;
}
