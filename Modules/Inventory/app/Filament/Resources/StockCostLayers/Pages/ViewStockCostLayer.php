<?php

namespace Modules\Inventory\Filament\Resources\StockCostLayers\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Inventory\Filament\Resources\StockCostLayers\StockCostLayerResource;

class ViewStockCostLayer extends ViewRecord
{
    protected static string $resource = StockCostLayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
