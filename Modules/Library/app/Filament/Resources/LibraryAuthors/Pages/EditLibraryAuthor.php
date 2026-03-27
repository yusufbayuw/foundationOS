<?php

namespace Modules\Library\Filament\Resources\LibraryAuthors\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Library\Filament\Resources\LibraryAuthors\LibraryAuthorResource;

class EditLibraryAuthor extends EditRecord
{
    protected static string $resource = LibraryAuthorResource::class;
}
