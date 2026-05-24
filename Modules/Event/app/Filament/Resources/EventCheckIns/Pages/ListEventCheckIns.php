<?php

namespace Modules\Event\Filament\Resources\EventCheckIns\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Event\Filament\Resources\EventCheckIns\EventCheckInResource;

class ListEventCheckIns extends ListRecords
{
    protected static string $resource = EventCheckInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
