<?php

namespace Modules\Helpdesk\Filament\Resources\TicketSatisfactions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Helpdesk\Filament\Resources\TicketSatisfactions\TicketSatisfactionResource;

class EditTicketSatisfaction extends EditRecord
{
    protected static string $resource = TicketSatisfactionResource::class;

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
