<?php

namespace Modules\Library\Filament\Resources\BookCopies\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Library\Filament\Resources\BookCopies\BookCopyResource;

class CreateBookCopy extends CreateRecord
{
    protected static string $resource = BookCopyResource::class;
}
