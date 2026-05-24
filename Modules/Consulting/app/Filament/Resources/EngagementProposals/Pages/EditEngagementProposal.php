<?php

namespace Modules\Consulting\Filament\Resources\EngagementProposals\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Consulting\Filament\Resources\EngagementProposals\EngagementProposalResource;

class EditEngagementProposal extends EditRecord
{
    protected static string $resource = EngagementProposalResource::class;

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
