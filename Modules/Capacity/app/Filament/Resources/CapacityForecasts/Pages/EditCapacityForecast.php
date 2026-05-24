<?php

namespace Modules\Capacity\Filament\Resources\CapacityForecasts\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Capacity\Filament\Resources\CapacityForecasts\CapacityForecastResource;

class EditCapacityForecast extends EditRecord
{
    protected static string $resource = CapacityForecastResource::class;

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
