<?php

namespace Modules\Library\Filament\Resources\LibraryLocations\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Library\Filament\Resources\LibraryLocations\LibraryLocationResource;

class ListLibraryLocations extends ListRecords
{
    protected static string $resource = LibraryLocationResource::class;
}
