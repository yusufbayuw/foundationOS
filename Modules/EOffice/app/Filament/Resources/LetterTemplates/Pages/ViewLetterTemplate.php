<?php

namespace Modules\EOffice\Filament\Resources\LetterTemplates\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\EOffice\Filament\Resources\LetterTemplates\LetterTemplateResource;

class ViewLetterTemplate extends ViewRecord
{
    protected static string $resource = LetterTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
