<?php

namespace Modules\Transport\Filament\Resources\RouteSchedules\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Transport\Filament\Resources\RouteSchedules\RouteScheduleResource;

class ViewRouteSchedule extends ViewRecord
{
    protected static string $resource = RouteScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
