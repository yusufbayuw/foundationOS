<?php

namespace Modules\Helpdesk\Observers;

use Modules\Helpdesk\Events\TicketCreated;
use Modules\Helpdesk\Models\Ticket;

class TicketObserver
{
    public function created(Ticket $ticket): void
    {
        event(new TicketCreated($ticket));
    }
}
