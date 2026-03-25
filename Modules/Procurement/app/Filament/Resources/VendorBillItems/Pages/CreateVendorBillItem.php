<?php

namespace Modules\Procurement\Filament\Resources\VendorBillItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Procurement\Filament\Resources\VendorBillItems\VendorBillItemResource;

class CreateVendorBillItem extends CreateRecord
{
    protected static string $resource = VendorBillItemResource::class;
}
