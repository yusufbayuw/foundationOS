<?php

namespace Modules\Helpdesk\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Helpdesk\Events\TicketCreated;
use Modules\Helpdesk\Listeners\AssignTicketToAgent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TicketCreated::class => [
            AssignTicketToAgent::class,
        ],
    ];
}
