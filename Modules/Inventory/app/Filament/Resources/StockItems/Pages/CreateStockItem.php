<?php

namespace Modules\Inventory\Filament\Resources\StockItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Inventory\Filament\Resources\StockItems\StockItemResource;

class CreateStockItem extends CreateRecord
{
    protected static string $resource = StockItemResource::class;
}
