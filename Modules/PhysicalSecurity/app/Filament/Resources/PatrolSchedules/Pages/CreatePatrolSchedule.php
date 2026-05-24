<?php

namespace Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\PhysicalSecurity\Filament\Resources\PatrolSchedules\PatrolScheduleResource;

class CreatePatrolSchedule extends CreateRecord
{
    protected static string $resource = PatrolScheduleResource::class;
}
