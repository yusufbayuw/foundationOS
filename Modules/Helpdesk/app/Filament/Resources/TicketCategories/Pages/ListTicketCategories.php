<?php

namespace Modules\Helpdesk\Filament\Resources\TicketCategories\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Helpdesk\Filament\Resources\TicketCategories\TicketCategoryResource;

class ListTicketCategories extends ListRecords
{
    protected static string $resource = TicketCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
