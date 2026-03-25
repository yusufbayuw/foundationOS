<?php

namespace Modules\Global\Filament\Resources\Timezones\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Global\Filament\Resources\Timezones\TimezoneResource;

class ViewTimezone extends ViewRecord
{
    protected static string $resource = TimezoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
