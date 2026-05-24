<?php

namespace Modules\Transport\Filament\Resources\BoardingLogs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Transport\Filament\Resources\BoardingLogs\BoardingLogResource;

class ListBoardingLogs extends ListRecords
{
    protected static string $resource = BoardingLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
