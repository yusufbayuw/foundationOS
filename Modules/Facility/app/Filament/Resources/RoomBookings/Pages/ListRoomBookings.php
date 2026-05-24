<?php

namespace Modules\Facility\Filament\Resources\RoomBookings\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Facility\Filament\Resources\RoomBookings\RoomBookingResource;

class ListRoomBookings extends ListRecords
{
    protected static string $resource = RoomBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
