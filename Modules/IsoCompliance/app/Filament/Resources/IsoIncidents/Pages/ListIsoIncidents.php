<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoIncidents\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\IsoCompliance\Filament\Resources\IsoIncidents\IsoIncidentResource;

class ListIsoIncidents extends ListRecords
{
    protected static string $resource = IsoIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
