<?php

namespace Modules\Event\Filament\Resources\EventTickets\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Event\Filament\Resources\EventTickets\EventTicketResource;

class CreateEventTicket extends CreateRecord
{
    protected static string $resource = EventTicketResource::class;
}
