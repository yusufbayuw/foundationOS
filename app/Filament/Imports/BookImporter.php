<?php

namespace App\Filament\Imports;

use Modules\Library\Models\Book;

class BookImporter extends BaseModelImporter
{
    protected static ?string $model = Book::class;
}
