<?php

namespace Modules\Library\Filament\Resources\LibraryAuthors\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Library\Filament\Resources\LibraryAuthors\LibraryAuthorResource;

class CreateLibraryAuthor extends CreateRecord
{
    protected static string $resource = LibraryAuthorResource::class;
}
