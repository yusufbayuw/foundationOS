<?php

namespace Modules\Helpdesk\Filament\Resources\TicketSatisfactions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Helpdesk\Filament\Resources\TicketSatisfactions\TicketSatisfactionResource;

class ViewTicketSatisfaction extends ViewRecord
{
    protected static string $resource = TicketSatisfactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
