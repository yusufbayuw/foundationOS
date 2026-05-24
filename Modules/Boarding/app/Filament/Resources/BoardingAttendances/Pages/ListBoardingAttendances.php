<?php

namespace Modules\Boarding\Filament\Resources\BoardingAttendances\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Boarding\Filament\Resources\BoardingAttendances\BoardingAttendanceResource;

class ListBoardingAttendances extends ListRecords
{
    protected static string $resource = BoardingAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
