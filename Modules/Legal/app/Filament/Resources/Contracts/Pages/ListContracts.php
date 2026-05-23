<?php

namespace Modules\Legal\Filament\Resources\Contracts\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Legal\Filament\Resources\Contracts\ContractResource;

class ListContracts extends ListRecords
{
    protected static string $resource = ContractResource::class;
}
