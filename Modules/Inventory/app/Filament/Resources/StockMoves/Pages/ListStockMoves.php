<?php

namespace Modules\Inventory\Filament\Resources\StockMoves\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Inventory\Filament\Resources\StockMoves\StockMoveResource;

class ListStockMoves extends ListRecords
{
    protected static string $resource = StockMoveResource::class;
}
