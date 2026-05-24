<?php

namespace Modules\Event\Filament\Resources\EventVendors\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Event\Filament\Resources\EventVendors\EventVendorResource;

class CreateEventVendor extends CreateRecord
{
    protected static string $resource = EventVendorResource::class;
}
