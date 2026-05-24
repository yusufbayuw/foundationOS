<?php

namespace Modules\Event\Filament\Resources\EventParticipants\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Event\Filament\Resources\EventParticipants\EventParticipantResource;

class ViewEventParticipant extends ViewRecord
{
    protected static string $resource = EventParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
