<?php

namespace Modules\Transport\Filament\Resources\BoardingLogs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Transport\Filament\Resources\BoardingLogs\BoardingLogResource;

class ViewBoardingLog extends ViewRecord
{
    protected static string $resource = BoardingLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
