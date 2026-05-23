<?php

namespace Modules\Capacity\Filament\Resources\CapacityResources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Capacity\Filament\Resources\CapacityResources\CapacityResourceResource;

class CreateCapacityResource extends CreateRecord
{
    protected static string $resource = CapacityResourceResource::class;
}
