<?php

namespace Modules\Transport\Filament\Resources\VehicleMaintenances\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Transport\Filament\Resources\VehicleMaintenances\VehicleMaintenanceResource;

class ListVehicleMaintenances extends ListRecords
{
    protected static string $resource = VehicleMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
