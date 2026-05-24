<?php

namespace Modules\Helpdesk\Filament\Resources\TicketComments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Helpdesk\Filament\Resources\TicketComments\TicketCommentResource;

class ListTicketComments extends ListRecords
{
    protected static string $resource = TicketCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
