<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoIncidents\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\IsoCompliance\Filament\Resources\IsoIncidents\IsoIncidentResource;

class ViewIsoIncident extends ViewRecord
{
    protected static string $resource = IsoIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
