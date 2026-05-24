<?php

namespace Modules\Transport\Filament\Resources\VehicleOperatingCosts\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Transport\Filament\Resources\VehicleOperatingCosts\VehicleOperatingCostResource;

class ViewVehicleOperatingCost extends ViewRecord
{
    protected static string $resource = VehicleOperatingCostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
