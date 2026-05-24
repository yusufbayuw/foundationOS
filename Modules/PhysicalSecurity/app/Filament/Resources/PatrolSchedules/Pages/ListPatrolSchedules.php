<?php

namespace Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules\PatrolScheduleResource;

class ListPatrolSchedules extends ListRecords
{
    protected static string $resource = PatrolScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
