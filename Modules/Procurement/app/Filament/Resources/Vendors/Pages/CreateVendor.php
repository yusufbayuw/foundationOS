<?php

namespace Modules\Procurement\Filament\Resources\Vendors\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Procurement\Filament\Resources\Vendors\VendorResource;

class CreateVendor extends CreateRecord
{
    protected static string $resource = VendorResource::class;
}
