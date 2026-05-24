<?php

namespace Modules\Capacity\Filament\Resources\CapacityForecasts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Capacity\Filament\Resources\CapacityForecasts\CapacityForecastResource;

class CreateCapacityForecast extends CreateRecord
{
    protected static string $resource = CapacityForecastResource::class;
}
