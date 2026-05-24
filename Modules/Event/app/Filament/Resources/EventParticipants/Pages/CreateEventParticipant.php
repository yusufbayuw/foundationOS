<?php

namespace Modules\Event\Filament\Resources\EventParticipants\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Event\Filament\Resources\EventParticipants\EventParticipantResource;

class CreateEventParticipant extends CreateRecord
{
    protected static string $resource = EventParticipantResource::class;
}
