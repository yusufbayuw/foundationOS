<?php

namespace Modules\Property\Filament\Resources\PropertyMaintenances\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Property\Filament\Resources\PropertyMaintenances\PropertyMaintenanceResource;

class ViewPropertyMaintenance extends ViewRecord
{
    protected static string $resource = PropertyMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
