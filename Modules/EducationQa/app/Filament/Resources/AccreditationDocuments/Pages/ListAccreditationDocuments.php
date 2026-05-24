<?php

namespace Modules\EducationQa\Filament\Resources\AccreditationDocuments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\EducationQa\Filament\Resources\AccreditationDocuments\AccreditationDocumentResource;

class ListAccreditationDocuments extends ListRecords
{
    protected static string $resource = AccreditationDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
