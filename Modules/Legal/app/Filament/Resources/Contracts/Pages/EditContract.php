<?php

namespace Modules\Legal\Filament\Resources\Contracts\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Legal\Filament\Resources\Contracts\ContractResource;

class EditContract extends EditRecord
{
    protected static string $resource = ContractResource::class;
}
