<?php

namespace Modules\Boarding\Filament\Resources\RoomInspections\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Boarding\Filament\Resources\RoomInspections\RoomInspectionResource;

class ViewRoomInspection extends ViewRecord
{
    protected static string $resource = RoomInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
