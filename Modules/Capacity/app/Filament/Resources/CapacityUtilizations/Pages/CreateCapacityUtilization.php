<?php

namespace Modules\Capacity\Filament\Resources\CapacityUtilizations\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Capacity\Filament\Resources\CapacityUtilizations\CapacityUtilizationResource;

class CreateCapacityUtilization extends CreateRecord
{
    protected static string $resource = CapacityUtilizationResource::class;
}
