<?php

namespace App\Filament\Imports;

use Modules\Inventory\Models\StockItem;

class StockItemImporter extends BaseModelImporter
{
    protected static ?string $model = StockItem::class;
}
