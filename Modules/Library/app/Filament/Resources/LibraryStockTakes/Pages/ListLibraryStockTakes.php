<?php

namespace Modules\Library\Filament\Resources\LibraryStockTakes\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Library\Filament\Resources\LibraryStockTakes\LibraryStockTakeResource;

class ListLibraryStockTakes extends ListRecords
{
    protected static string $resource = LibraryStockTakeResource::class;
}
