<?php

namespace Modules\Facility\Filament\Resources\RoomBookings\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Facility\Filament\Resources\RoomBookings\RoomBookingResource;

class ViewRoomBooking extends ViewRecord
{
    protected static string $resource = RoomBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
