<?php

namespace Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules\PatrolScheduleResource;

class ViewPatrolSchedule extends ViewRecord
{
    protected static string $resource = PatrolScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
