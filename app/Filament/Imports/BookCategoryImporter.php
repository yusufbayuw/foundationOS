<?php

namespace App\Filament\Imports;

use Modules\Library\Models\BookCategory;

class BookCategoryImporter extends BaseModelImporter
{
    protected static ?string $model = BookCategory::class;
}
