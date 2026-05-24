<?php

namespace Modules\Property\Filament\Resources\PropertyMaintenances\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Property\Filament\Resources\PropertyMaintenances\PropertyMaintenanceResource;

class ListPropertyMaintenances extends ListRecords
{
    protected static string $resource = PropertyMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
