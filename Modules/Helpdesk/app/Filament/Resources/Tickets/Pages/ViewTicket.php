<?php

namespace Modules\Helpdesk\Filament\Resources\Tickets\Pages;

use Filament\Resources\Pages\ViewRecord;
use Modules\Helpdesk\Filament\Resources\Tickets\TicketResource;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;
}
