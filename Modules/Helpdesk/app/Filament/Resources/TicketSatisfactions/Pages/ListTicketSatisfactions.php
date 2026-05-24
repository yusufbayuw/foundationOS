<?php

namespace Modules\Helpdesk\Filament\Resources\TicketSatisfactions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Helpdesk\Filament\Resources\TicketSatisfactions\TicketSatisfactionResource;

class ListTicketSatisfactions extends ListRecords
{
    protected static string $resource = TicketSatisfactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
