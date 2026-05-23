<?php

namespace Modules\Inventory\Filament\Resources\StockItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Inventory\Filament\Resources\StockItems\StockItemResource;

class ListStockItems extends ListRecords
{
    protected static string $resource = StockItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
