<?php

namespace Modules\Helpdesk\Filament\Resources\TicketComments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Helpdesk\Filament\Resources\TicketComments\TicketCommentResource;

class CreateTicketComment extends CreateRecord
{
    protected static string $resource = TicketCommentResource::class;
}
