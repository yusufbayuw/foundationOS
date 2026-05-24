<?php

namespace Modules\PhysicalSecurity\Filament\Resources\SecurityIncidents\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\PhysicalSecurity\Filament\Resources\SecurityIncidents\SecurityIncidentResource;

class CreateSecurityIncident extends CreateRecord
{
    protected static string $resource = SecurityIncidentResource::class;
}
