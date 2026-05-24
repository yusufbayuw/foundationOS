<?php

namespace Modules\IsoCompliance\Filament\Resources\IsoPolicies\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\IsoCompliance\Filament\Resources\IsoPolicies\IsoPolicyResource;

class CreateIsoPolicy extends CreateRecord
{
    protected static string $resource = IsoPolicyResource::class;
}
