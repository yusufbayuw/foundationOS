<?php

namespace Modules\Library\Filament\Resources\LibraryAuthors\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Library\Filament\Resources\LibraryAuthors\LibraryAuthorResource;

class ListLibraryAuthors extends ListRecords
{
    protected static string $resource = LibraryAuthorResource::class;
}
