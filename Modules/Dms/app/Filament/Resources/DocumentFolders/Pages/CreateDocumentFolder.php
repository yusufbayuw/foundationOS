<?php

namespace Modules\Dms\Filament\Resources\DocumentFolders\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Dms\Filament\Resources\DocumentFolders\DocumentFolderResource;

class CreateDocumentFolder extends CreateRecord
{
    protected static string $resource = DocumentFolderResource::class;
}
