<?php

namespace Modules\Library\Filament\Resources\Books\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Library\Filament\Resources\Books\BookResource;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;
}
