<?php

namespace Modules\EOffice\Filament\Resources\LetterCategories\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\EOffice\Filament\Resources\LetterCategories\LetterCategoryResource;

class ViewLetterCategory extends ViewRecord
{
    protected static string $resource = LetterCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
