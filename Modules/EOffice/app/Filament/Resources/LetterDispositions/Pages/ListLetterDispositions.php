<?php

namespace Modules\EOffice\Filament\Resources\LetterDispositions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\EOffice\Filament\Resources\LetterDispositions\LetterDispositionResource;

class ListLetterDispositions extends ListRecords
{
    protected static string $resource = LetterDispositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
