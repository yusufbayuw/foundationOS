<?php

namespace Modules\Capacity\Filament\Resources\CapacityForecasts\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Capacity\Filament\Resources\CapacityForecasts\CapacityForecastResource;

class ViewCapacityForecast extends ViewRecord
{
    protected static string $resource = CapacityForecastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
