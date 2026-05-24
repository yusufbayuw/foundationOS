<?php

namespace Modules\Inventory\Filament\Resources\StockCostLayers\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Inventory\Filament\Resources\StockCostLayers\StockCostLayerResource;

class EditStockCostLayer extends EditRecord
{
    protected static string $resource = StockCostLayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
