<?php

namespace Modules\Consulting\Filament\Resources\EngagementProposals\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Consulting\Filament\Resources\EngagementProposals\EngagementProposalResource;

class ListEngagementProposals extends ListRecords
{
    protected static string $resource = EngagementProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
