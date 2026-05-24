<?php

namespace Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts\EmergencyAlertResource;

class ViewEmergencyAlert extends ViewRecord
{
    protected static string $resource = EmergencyAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
