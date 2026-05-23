<?php

namespace Modules\Inventory\Filament\Resources\StockAdjustments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Inventory\Filament\Resources\StockAdjustments\StockAdjustmentResource;

class CreateStockAdjustment extends CreateRecord
{
    protected static string $resource = StockAdjustmentResource::class;
}
