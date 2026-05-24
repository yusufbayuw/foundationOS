<?php

namespace Modules\Helpdesk\Filament\Resources\TicketAttachments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Helpdesk\Filament\Resources\TicketAttachments\TicketAttachmentResource;

class CreateTicketAttachment extends CreateRecord
{
    protected static string $resource = TicketAttachmentResource::class;
}
