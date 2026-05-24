<?php

namespace Modules\Facility\Filament\Resources\RoomEquipment\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Facility\Filament\Resources\RoomEquipment\RoomEquipmentResource;

class ViewRoomEquipment extends ViewRecord
{
    protected static string $resource = RoomEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
