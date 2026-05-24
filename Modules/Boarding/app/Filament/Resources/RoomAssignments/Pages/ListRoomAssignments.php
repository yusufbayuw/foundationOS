<?php

namespace Modules\Boarding\Filament\Resources\RoomAssignments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Boarding\Filament\Resources\RoomAssignments\RoomAssignmentResource;

class ListRoomAssignments extends ListRecords
{
    protected static string $resource = RoomAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
