<?php

namespace Modules\Legal\Filament\Resources\ContractParties\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Legal\Filament\Resources\ContractParties\ContractPartyResource;

class CreateContractParty extends CreateRecord
{
    protected static string $resource = ContractPartyResource::class;
}
