<?php

namespace Modules\Global\Filament\Resources\Timezones\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Global\Filament\Resources\Timezones\TimezoneResource;

class ListTimezones extends ListRecords
{
    protected static string $resource = TimezoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
