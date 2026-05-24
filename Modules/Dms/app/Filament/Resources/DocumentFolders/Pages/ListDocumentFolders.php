<?php

namespace Modules\Dms\Filament\Resources\DocumentFolders\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Dms\Filament\Resources\DocumentFolders\DocumentFolderResource;

class ListDocumentFolders extends ListRecords
{
    protected static string $resource = DocumentFolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
