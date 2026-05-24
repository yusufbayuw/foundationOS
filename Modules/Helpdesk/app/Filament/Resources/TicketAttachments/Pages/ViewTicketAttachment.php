<?php

namespace Modules\Helpdesk\Filament\Resources\TicketAttachments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Helpdesk\Filament\Resources\TicketAttachments\TicketAttachmentResource;

class ViewTicketAttachment extends ViewRecord
{
    protected static string $resource = TicketAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
