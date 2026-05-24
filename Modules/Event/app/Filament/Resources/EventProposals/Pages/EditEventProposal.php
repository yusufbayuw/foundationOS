<?php

namespace Modules\Event\Filament\Resources\EventProposals\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Event\Filament\Resources\EventProposals\EventProposalResource;

class EditEventProposal extends EditRecord
{
    protected static string $resource = EventProposalResource::class;

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
