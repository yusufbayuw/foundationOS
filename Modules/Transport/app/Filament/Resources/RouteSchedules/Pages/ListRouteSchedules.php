<?php

namespace Modules\Transport\Filament\Resources\RouteSchedules\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Transport\Filament\Resources\RouteSchedules\RouteScheduleResource;

class ListRouteSchedules extends ListRecords
{
    protected static string $resource = RouteScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
