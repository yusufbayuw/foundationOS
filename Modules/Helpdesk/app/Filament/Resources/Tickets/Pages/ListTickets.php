<?php

namespace Modules\Helpdesk\Filament\Resources\Tickets\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Helpdesk\Filament\Resources\Tickets\TicketResource;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;
}
