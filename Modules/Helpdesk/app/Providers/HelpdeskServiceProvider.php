<?php

namespace Modules\Helpdesk\Providers;

use Modules\Helpdesk\Models\Ticket;
use Modules\Helpdesk\Observers\TicketObserver;
use Nwidart\Modules\Support\ModuleServiceProvider;

class HelpdeskServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Helpdesk';

    protected string $nameLower = 'helpdesk';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        Ticket::observe(TicketObserver::class);
    }
}
