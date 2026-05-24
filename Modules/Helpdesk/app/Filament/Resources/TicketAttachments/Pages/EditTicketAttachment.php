<?php

namespace Modules\Helpdesk\Filament\Resources\TicketAttachments\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Helpdesk\Filament\Resources\TicketAttachments\TicketAttachmentResource;

class EditTicketAttachment extends EditRecord
{
    protected static string $resource = TicketAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
