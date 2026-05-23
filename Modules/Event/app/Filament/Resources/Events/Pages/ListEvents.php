<?php

namespace Modules\Event\Filament\Resources\Events\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Event\Filament\Resources\Events\EventResource;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;
}
