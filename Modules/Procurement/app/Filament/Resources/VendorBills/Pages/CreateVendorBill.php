<?php

namespace Modules\Procurement\Filament\Resources\VendorBills\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Procurement\Filament\Resources\VendorBills\VendorBillResource;

class CreateVendorBill extends CreateRecord
{
    protected static string $resource = VendorBillResource::class;
}
