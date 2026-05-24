<?php

namespace Modules\Boarding\Filament\Resources\RoomInspections\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Boarding\Filament\Resources\RoomInspections\RoomInspectionResource;

class EditRoomInspection extends EditRecord
{
    protected static string $resource = RoomInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
