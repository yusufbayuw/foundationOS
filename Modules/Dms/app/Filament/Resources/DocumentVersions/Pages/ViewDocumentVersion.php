<?php

namespace Modules\Dms\Filament\Resources\DocumentVersions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Dms\Filament\Resources\DocumentVersions\DocumentVersionResource;

class ViewDocumentVersion extends ViewRecord
{
    protected static string $resource = DocumentVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
