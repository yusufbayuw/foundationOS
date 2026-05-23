<?php

namespace Modules\Legal\Filament\Resources\Contracts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Legal\Filament\Resources\Contracts\ContractResource;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;
}
