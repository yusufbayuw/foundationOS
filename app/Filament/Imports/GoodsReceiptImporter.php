<?php

namespace App\Filament\Imports;

use Modules\Procurement\Models\GoodsReceipt;

class GoodsReceiptImporter extends BaseModelImporter
{
    protected static ?string $model = GoodsReceipt::class;
}
