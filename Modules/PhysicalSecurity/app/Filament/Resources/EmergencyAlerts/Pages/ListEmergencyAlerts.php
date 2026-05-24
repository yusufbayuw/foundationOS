<?php

namespace Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\PhysicalSecurity\Filament\Resources\EmergencyAlerts\EmergencyAlertResource;

class ListEmergencyAlerts extends ListRecords
{
    protected static string $resource = EmergencyAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
