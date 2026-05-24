<?php

namespace Modules\Dms\Filament\Resources\DocumentFolders\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Dms\Filament\Resources\DocumentFolders\DocumentFolderResource;

class ViewDocumentFolder extends ViewRecord
{
    protected static string $resource = DocumentFolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
