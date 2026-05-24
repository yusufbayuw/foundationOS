<?php

namespace Modules\Facility\Filament\Resources\RoomEquipment\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Facility\Filament\Resources\RoomEquipment\RoomEquipmentResource;

class ListRoomEquipment extends ListRecords
{
    protected static string $resource = RoomEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
