<?php

namespace Modules\Risk\Filament\Resources\RiskIncidents\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Risk\Filament\Resources\RiskIncidents\RiskIncidentResource;

class CreateRiskIncident extends CreateRecord
{
    protected static string $resource = RiskIncidentResource::class;
}
