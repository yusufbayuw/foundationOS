<?php

namespace Modules\Boarding\Filament\Resources\BoardingAttendances\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Boarding\Filament\Resources\BoardingAttendances\BoardingAttendanceResource;

class EditBoardingAttendance extends EditRecord
{
    protected static string $resource = BoardingAttendanceResource::class;

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
