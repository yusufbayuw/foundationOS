<?php

namespace Modules\Boarding\Filament\Resources\RoomAssignments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Boarding\Filament\Resources\RoomAssignments\RoomAssignmentResource;

class ViewRoomAssignment extends ViewRecord
{
    protected static string $resource = RoomAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
