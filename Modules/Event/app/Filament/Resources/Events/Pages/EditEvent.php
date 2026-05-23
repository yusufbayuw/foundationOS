<?php

namespace Modules\Event\Filament\Resources\Events\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Event\Filament\Resources\Events\EventResource;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;
}
