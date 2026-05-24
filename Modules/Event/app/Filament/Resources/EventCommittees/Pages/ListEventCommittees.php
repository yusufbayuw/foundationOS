<?php

namespace Modules\Event\Filament\Resources\EventCommittees\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Event\Filament\Resources\EventCommittees\EventCommitteeResource;

class ListEventCommittees extends ListRecords
{
    protected static string $resource = EventCommitteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
