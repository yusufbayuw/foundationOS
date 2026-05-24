<?php

namespace Modules\Event\Filament\Resources\EventProposals\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Event\Filament\Resources\EventProposals\EventProposalResource;

class ListEventProposals extends ListRecords
{
    protected static string $resource = EventProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
