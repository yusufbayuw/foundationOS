<?php

namespace Modules\PhysicalSecurity\Filament\Resources\PatrolLogs\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\PhysicalSecurity\Filament\Resources\PatrolLogs\PatrolLogResource;

class CreatePatrolLog extends CreateRecord
{
    protected static string $resource = PatrolLogResource::class;
}
