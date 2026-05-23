<?php

namespace Modules\Legal\Filament\Resources\Contracts\Pages;

use Filament\Resources\Pages\ViewRecord;
use Modules\Legal\Filament\Resources\Contracts\ContractResource;

class ViewContract extends ViewRecord
{
    protected static string $resource = ContractResource::class;
}
