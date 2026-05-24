<?php

namespace Modules\Helpdesk\Filament\Resources\TicketAttachments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Helpdesk\Filament\Resources\TicketAttachments\TicketAttachmentResource;

class ListTicketAttachments extends ListRecords
{
    protected static string $resource = TicketAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
