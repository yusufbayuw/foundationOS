<?php

namespace App\Filament\Imports;

use Modules\Procurement\Models\PurchaseOrder;

class PurchaseOrderImporter extends BaseModelImporter
{
    protected static ?string $model = PurchaseOrder::class;
}
