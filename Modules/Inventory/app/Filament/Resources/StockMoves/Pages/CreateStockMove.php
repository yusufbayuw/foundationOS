<?php

namespace Modules\Inventory\Filament\Resources\StockMoves\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Inventory\Filament\Resources\StockMoves\StockMoveResource;

class CreateStockMove extends CreateRecord
{
    protected static string $resource = StockMoveResource::class;
}
