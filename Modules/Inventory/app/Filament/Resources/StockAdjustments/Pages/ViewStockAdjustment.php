<?php

namespace Modules\Inventory\Filament\Resources\StockAdjustments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Inventory\Filament\Resources\StockAdjustments\StockAdjustmentResource;

class ViewStockAdjustment extends ViewRecord
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
