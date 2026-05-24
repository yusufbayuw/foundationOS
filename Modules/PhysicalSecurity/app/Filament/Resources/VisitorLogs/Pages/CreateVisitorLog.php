<?php

namespace Modules\PhysicalSecurity\Filament\Resources\VisitorLogs\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\PhysicalSecurity\Filament\Resources\VisitorLogs\VisitorLogResource;

class CreateVisitorLog extends CreateRecord
{
    protected static string $resource = VisitorLogResource::class;
}
