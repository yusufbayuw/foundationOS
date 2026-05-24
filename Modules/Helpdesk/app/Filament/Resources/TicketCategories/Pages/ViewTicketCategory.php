<?php

namespace Modules\Helpdesk\Filament\Resources\TicketCategories\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Helpdesk\Filament\Resources\TicketCategories\TicketCategoryResource;

class ViewTicketCategory extends ViewRecord
{
    protected static string $resource = TicketCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
