<?php

namespace Modules\Boarding\Filament\Resources\RoomInspections\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Boarding\Filament\Resources\RoomInspections\RoomInspectionResource;

class ListRoomInspections extends ListRecords
{
    protected static string $resource = RoomInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
