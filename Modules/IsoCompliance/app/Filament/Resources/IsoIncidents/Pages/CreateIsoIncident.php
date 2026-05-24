<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoIncidents\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\IsoCompliance\Filament\Resources\IsoIncidents\IsoIncidentResource;

class CreateIsoIncident extends CreateRecord
{
    protected static string $resource = IsoIncidentResource::class;
}
