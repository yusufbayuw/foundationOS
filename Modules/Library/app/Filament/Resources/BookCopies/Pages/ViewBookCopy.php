<?php

namespace Modules\Library\Filament\Resources\BookCopies\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Library\Filament\Resources\BookCopies\BookCopyResource;

class ViewBookCopy extends ViewRecord
{
    protected static string $resource = BookCopyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
