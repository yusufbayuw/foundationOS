<?php

namespace Modules\Campus\Filament\Resources\FeederLogs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\FeederLogs\FeederLogResource;

class ListFeederLogs extends ListRecords
{
    protected static string $resource = FeederLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
