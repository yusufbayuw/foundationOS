<?php

namespace Modules\Library\Filament\Resources\BookCategories\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Library\Filament\Resources\BookCategories\BookCategoryResource;

class CreateBookCategory extends CreateRecord
{
    protected static string $resource = BookCategoryResource::class;
}
