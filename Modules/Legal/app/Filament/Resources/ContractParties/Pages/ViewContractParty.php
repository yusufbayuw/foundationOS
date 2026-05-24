<?php

namespace Modules\Legal\Filament\Resources\ContractParties\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Legal\Filament\Resources\ContractParties\ContractPartyResource;

class ViewContractParty extends ViewRecord
{
    protected static string $resource = ContractPartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
