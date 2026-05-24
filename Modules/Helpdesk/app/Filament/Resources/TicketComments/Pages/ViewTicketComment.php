<?php

namespace Modules\Helpdesk\Filament\Resources\TicketComments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Helpdesk\Filament\Resources\TicketComments\TicketCommentResource;

class ViewTicketComment extends ViewRecord
{
    protected static string $resource = TicketCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
