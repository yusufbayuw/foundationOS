<?php

namespace Modules\Legal\Filament\Resources\ContractParties\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Legal\Filament\Resources\ContractParties\ContractPartyResource;

class ListContractParties extends ListRecords
{
    protected static string $resource = ContractPartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
