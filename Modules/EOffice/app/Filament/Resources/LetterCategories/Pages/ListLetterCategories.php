<?php

namespace Modules\EOffice\Filament\Resources\LetterCategories\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\EOffice\Filament\Resources\LetterCategories\LetterCategoryResource;

class ListLetterCategories extends ListRecords
{
    protected static string $resource = LetterCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
