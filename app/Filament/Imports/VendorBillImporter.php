<?php

namespace App\Filament\Imports;

use Modules\Procurement\Models\VendorBill;

class VendorBillImporter extends BaseModelImporter
{
    protected static ?string $model = VendorBill::class;
}
