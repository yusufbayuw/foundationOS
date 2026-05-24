<?php

namespace Modules\Inventory\Filament\Resources\StockAdjustmentLines\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Inventory\Filament\Resources\StockAdjustmentLines\StockAdjustmentLineResource;

class ViewStockAdjustmentLine extends ViewRecord
{
    protected static string $resource = StockAdjustmentLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
