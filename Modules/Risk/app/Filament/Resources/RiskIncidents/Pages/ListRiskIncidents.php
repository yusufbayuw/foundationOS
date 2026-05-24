<?php

namespace Modules\Risk\Filament\Resources\RiskIncidents\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Risk\Filament\Resources\RiskIncidents\RiskIncidentResource;

class ListRiskIncidents extends ListRecords
{
    protected static string $resource = RiskIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
