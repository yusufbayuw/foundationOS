<?php

namespace Modules\Inventory\Filament\Resources\StockAdjustmentLines\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Inventory\Filament\Resources\StockAdjustmentLines\StockAdjustmentLineResource;

class EditStockAdjustmentLine extends EditRecord
{
    protected static string $resource = StockAdjustmentLineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
