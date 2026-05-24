<?php

namespace Modules\Consulting\Filament\Resources\EngagementProposals\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Consulting\Filament\Resources\EngagementProposals\EngagementProposalResource;

class ViewEngagementProposal extends ViewRecord
{
    protected static string $resource = EngagementProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
