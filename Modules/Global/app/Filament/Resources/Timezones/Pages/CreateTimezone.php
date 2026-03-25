<?php

namespace Modules\Global\Filament\Resources\Timezones\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Global\Filament\Resources\Timezones\TimezoneResource;

class CreateTimezone extends CreateRecord
{
    protected static string $resource = TimezoneResource::class;
}
