<?php

namespace Modules\Event\Filament\Resources\Events\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Event\Filament\Resources\Events\EventResource;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;
}
