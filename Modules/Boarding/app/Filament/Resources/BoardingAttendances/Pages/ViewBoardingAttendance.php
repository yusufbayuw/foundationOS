<?php

namespace Modules\Boarding\Filament\Resources\BoardingAttendances\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Boarding\Filament\Resources\BoardingAttendances\BoardingAttendanceResource;

class ViewBoardingAttendance extends ViewRecord
{
    protected static string $resource = BoardingAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
