<?php

namespace Modules\Event\Filament\Resources\EventCheckIns\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Event\Filament\Resources\EventCheckIns\EventCheckInResource;

class CreateEventCheckIn extends CreateRecord
{
    protected static string $resource = EventCheckInResource::class;
}
