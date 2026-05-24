<?php

namespace Modules\Risk\Filament\Resources\RiskIncidents\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Risk\Filament\Resources\RiskIncidents\RiskIncidentResource;

class EditRiskIncident extends EditRecord
{
    protected static string $resource = RiskIncidentResource::class;
}
