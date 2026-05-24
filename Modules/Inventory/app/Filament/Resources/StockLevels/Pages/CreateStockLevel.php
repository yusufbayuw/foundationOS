<?php

namespace Modules\Inventory\Filament\Resources\StockLevels\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Inventory\Filament\Resources\StockLevels\StockLevelResource;

class CreateStockLevel extends CreateRecord
{
    protected static string $resource = StockLevelResource::class;
}
