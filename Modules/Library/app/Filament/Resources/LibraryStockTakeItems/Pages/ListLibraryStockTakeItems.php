<?php

namespace Modules\Library\Filament\Resources\LibraryStockTakeItems\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Library\Filament\Resources\LibraryStockTakeItems\LibraryStockTakeItemResource;

class ListLibraryStockTakeItems extends ListRecords
{
    protected static string $resource = LibraryStockTakeItemResource::class;
}
