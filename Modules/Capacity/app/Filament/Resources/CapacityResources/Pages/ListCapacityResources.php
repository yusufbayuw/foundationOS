<?php

namespace Modules\Capacity\Filament\Resources\CapacityResources\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Capacity\Filament\Resources\CapacityResources\CapacityResourceResource;

class ListCapacityResources extends ListRecords
{
    protected static string $resource = CapacityResourceResource::class;
}
