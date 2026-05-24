<?php

namespace Modules\Event\Filament\Resources\EventProposals\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Event\Filament\Resources\EventProposals\EventProposalResource;

class ViewEventProposal extends ViewRecord
{
    protected static string $resource = EventProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
