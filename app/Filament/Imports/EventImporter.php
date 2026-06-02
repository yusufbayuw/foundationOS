<?php

namespace App\Filament\Imports;

use Modules\Event\Models\Event;

class EventImporter extends BaseModelImporter
{
    protected static ?string $model = Event::class;
}
