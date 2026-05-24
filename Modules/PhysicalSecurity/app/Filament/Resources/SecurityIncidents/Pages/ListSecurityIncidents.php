<?php

namespace Modules\PhysicalSecurity\Filament\Resources\SecurityIncidents\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\PhysicalSecurity\Filament\Resources\SecurityIncidents\SecurityIncidentResource;

class ListSecurityIncidents extends ListRecords
{
    protected static string $resource = SecurityIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
