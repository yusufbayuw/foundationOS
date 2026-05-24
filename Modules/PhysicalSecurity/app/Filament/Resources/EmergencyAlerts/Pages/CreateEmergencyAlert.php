<?php

namespace Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts\EmergencyAlertResource;

class CreateEmergencyAlert extends CreateRecord
{
    protected static string $resource = EmergencyAlertResource::class;
}
