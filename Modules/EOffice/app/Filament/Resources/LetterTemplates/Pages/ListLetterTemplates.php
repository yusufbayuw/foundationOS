<?php

namespace Modules\EOffice\Filament\Resources\LetterTemplates\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\EOffice\Filament\Resources\LetterTemplates\LetterTemplateResource;

class ListLetterTemplates extends ListRecords
{
    protected static string $resource = LetterTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
