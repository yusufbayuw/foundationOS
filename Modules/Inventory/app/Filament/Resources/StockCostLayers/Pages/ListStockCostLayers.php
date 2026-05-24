<?php

namespace Modules\Inventory\Filament\Resources\StockCostLayers\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Inventory\Filament\Resources\StockCostLayers\StockCostLayerResource;

class ListStockCostLayers extends ListRecords
{
    protected static string $resource = StockCostLayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
