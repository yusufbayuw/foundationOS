<?php

namespace Modules\Event\Filament\Resources\EventParticipants\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Event\Filament\Resources\EventParticipants\EventParticipantResource;

class ListEventParticipants extends ListRecords
{
    protected static string $resource = EventParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
