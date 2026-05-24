<?php

namespace Modules\Event\Filament\Resources\EventTickets\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Event\Filament\Resources\EventTickets\EventTicketResource;

class ViewEventTicket extends ViewRecord
{
    protected static string $resource = EventTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
