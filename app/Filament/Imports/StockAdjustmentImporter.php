<?php

namespace App\Filament\Imports;

use Modules\Inventory\Models\StockAdjustment;

class StockAdjustmentImporter extends BaseModelImporter
{
    protected static ?string $model = StockAdjustment::class;
}
