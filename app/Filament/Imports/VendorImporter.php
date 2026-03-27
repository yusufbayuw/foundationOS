<?php

namespace App\Filament\Imports;

use Modules\Procurement\Models\Vendor;

class VendorImporter extends BaseModelImporter
{
    protected static ?string $model = Vendor::class;
}
