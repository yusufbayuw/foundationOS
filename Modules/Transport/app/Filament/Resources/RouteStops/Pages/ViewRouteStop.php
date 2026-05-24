<?php

namespace Modules\Transport\Filament\Resources\RouteStops\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Transport\Filament\Resources\RouteStops\RouteStopResource;

class ViewRouteStop extends ViewRecord
{
    protected static string $resource = RouteStopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
