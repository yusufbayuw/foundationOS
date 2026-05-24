<?php

namespace Modules\Helpdesk\Filament\Resources\TicketSatisfactions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Helpdesk\Filament\Resources\TicketSatisfactions\TicketSatisfactionResource;

class CreateTicketSatisfaction extends CreateRecord
{
    protected static string $resource = TicketSatisfactionResource::class;
}
