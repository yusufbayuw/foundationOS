<?php

namespace Modules\Facility\Filament\Resources\Buildings\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Facility\Filament\Resources\Buildings\BuildingResource;

class CreateBuilding extends CreateRecord
{
    protected static string $resource = BuildingResource::class;
}
