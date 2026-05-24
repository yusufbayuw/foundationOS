<?php

namespace Modules\Inventory\Filament\Resources\StockCostLayers\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Inventory\Filament\Resources\StockCostLayers\StockCostLayerResource;

class CreateStockCostLayer extends CreateRecord
{
    protected static string $resource = StockCostLayerResource::class;
}
