<?php

namespace Modules\Inventory\Filament\Resources\StockMoves\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Inventory\Filament\Resources\StockMoves\StockMoveResource;

class EditStockMove extends EditRecord
{
    protected static string $resource = StockMoveResource::class;
}
