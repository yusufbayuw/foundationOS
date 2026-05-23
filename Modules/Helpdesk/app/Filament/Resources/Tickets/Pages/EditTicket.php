<?php

namespace Modules\Helpdesk\Filament\Resources\Tickets\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Helpdesk\Filament\Resources\Tickets\TicketResource;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;
}
