<?php

namespace Modules\Risk\Filament\Resources\RiskIncidents\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Risk\Filament\Resources\RiskIncidents\RiskIncidentResource;

class ViewRiskIncident extends ViewRecord
{
    protected static string $resource = RiskIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
