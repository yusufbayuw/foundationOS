<?php

namespace Modules\Employee\Filament\Resources\Positions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Employee\Filament\Resources\Positions\PositionResource;

class CreatePosition extends CreateRecord
{
    protected static string $resource = PositionResource::class;
}
