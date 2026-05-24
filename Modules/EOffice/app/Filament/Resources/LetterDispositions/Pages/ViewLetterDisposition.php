<?php

namespace Modules\EOffice\Filament\Resources\LetterDispositions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\EOffice\Filament\Resources\LetterDispositions\LetterDispositionResource;

class ViewLetterDisposition extends ViewRecord
{
    protected static string $resource = LetterDispositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
