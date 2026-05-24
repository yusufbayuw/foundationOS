<?php

namespace Modules\Capacity\Filament\Resources\CapacityForecasts\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Capacity\Filament\Resources\CapacityForecasts\CapacityForecastResource;

class ListCapacityForecasts extends ListRecords
{
    protected static string $resource = CapacityForecastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
