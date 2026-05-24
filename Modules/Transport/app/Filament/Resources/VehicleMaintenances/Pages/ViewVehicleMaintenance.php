<?php

namespace Modules\Transport\Filament\Resources\VehicleMaintenances\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Transport\Filament\Resources\VehicleMaintenances\VehicleMaintenanceResource;

class ViewVehicleMaintenance extends ViewRecord
{
    protected static string $resource = VehicleMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
