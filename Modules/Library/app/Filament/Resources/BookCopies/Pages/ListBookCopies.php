<?php

namespace Modules\Library\Filament\Resources\BookCopies\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Library\Filament\Resources\BookCopies\BookCopyResource;

class ListBookCopies extends ListRecords
{
    protected static string $resource = BookCopyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
