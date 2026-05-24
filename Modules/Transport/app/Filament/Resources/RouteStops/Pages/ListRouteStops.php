<?php

namespace Modules\Transport\Filament\Resources\RouteStops\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Transport\Filament\Resources\RouteStops\RouteStopResource;

class ListRouteStops extends ListRecords
{
    protected static string $resource = RouteStopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
