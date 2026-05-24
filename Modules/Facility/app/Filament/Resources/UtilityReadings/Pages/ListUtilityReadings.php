<?php

namespace Modules\Facility\Filament\Resources\UtilityReadings\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Facility\Filament\Resources\UtilityReadings\UtilityReadingResource;

class ListUtilityReadings extends ListRecords
{
    protected static string $resource = UtilityReadingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
