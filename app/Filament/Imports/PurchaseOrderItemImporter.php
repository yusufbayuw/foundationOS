<?php

namespace App\Filament\Imports;

use Modules\Procurement\Models\PurchaseOrderItem;

class PurchaseOrderItemImporter extends BaseModelImporter
{
    protected static ?string $model = PurchaseOrderItem::class;
}
