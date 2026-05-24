<?php

namespace Modules\Inventory\Filament\Resources\StockAdjustmentLines\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Inventory\Filament\Resources\StockAdjustmentLines\StockAdjustmentLineResource;

class ListStockAdjustmentLines extends ListRecords
{
    protected static string $resource = StockAdjustmentLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
