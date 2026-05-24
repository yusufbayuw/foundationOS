<?php

namespace Modules\Inventory\Filament\Resources\StockAdjustmentLines\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Inventory\Filament\Resources\StockAdjustmentLines\StockAdjustmentLineResource;

class CreateStockAdjustmentLine extends CreateRecord
{
    protected static string $resource = StockAdjustmentLineResource::class;
}
