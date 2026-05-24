<?php

namespace Modules\Transport\Filament\Resources\VehicleOperatingCosts\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Transport\Filament\Resources\VehicleOperatingCosts\VehicleOperatingCostResource;

class ListVehicleOperatingCosts extends ListRecords
{
    protected static string $resource = VehicleOperatingCostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
