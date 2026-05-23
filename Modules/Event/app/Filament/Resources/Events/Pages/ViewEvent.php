<?php

namespace Modules\Event\Filament\Resources\Events\Pages;

use Filament\Resources\Pages\ViewRecord;
use Modules\Event\Filament\Resources\Events\EventResource;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;
}
