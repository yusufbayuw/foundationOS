<?php

namespace Modules\Library\Filament\Resources\LibraryItemStatuses\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Library\Filament\Resources\LibraryItemStatuses\LibraryItemStatusResource;

class ListLibraryItemStatuses extends ListRecords
{
    protected static string $resource = LibraryItemStatusResource::class;
}
