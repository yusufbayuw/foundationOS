<?php

namespace Modules\Facility\Filament\Resources\RoomMaintenanceLogs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Facility\Filament\Resources\RoomMaintenanceLogs\RoomMaintenanceLogResource;

class ListRoomMaintenanceLogs extends ListRecords
{
    protected static string $resource = RoomMaintenanceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
