<?php

namespace Modules\EducationQa\Filament\Resources\AccreditationDocuments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\EducationQa\Filament\Resources\AccreditationDocuments\AccreditationDocumentResource;

class ViewAccreditationDocument extends ViewRecord
{
    protected static string $resource = AccreditationDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
