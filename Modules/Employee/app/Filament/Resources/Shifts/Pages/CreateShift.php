<?php

namespace Modules\Employee\Filament\Resources\Shifts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Employee\Filament\Resources\Shifts\ShiftResource;

class CreateShift extends CreateRecord
{
    protected static string $resource = ShiftResource::class;
}
