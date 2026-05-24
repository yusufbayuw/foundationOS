<?php

namespace Modules\Facility\Filament\Resources\RoomMaintenanceLogs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Facility\Filament\Resources\RoomMaintenanceLogs\RoomMaintenanceLogResource;

class ViewRoomMaintenanceLog extends ViewRecord
{
    protected static string $resource = RoomMaintenanceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
