<?php

namespace Modules\Inventory\Filament\Resources\StockLevels\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Inventory\Filament\Resources\StockLevels\StockLevelResource;

class ViewStockLevel extends ViewRecord
{
    protected static string $resource = StockLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
