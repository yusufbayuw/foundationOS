<?php

namespace Modules\Dms\Filament\Resources\DocumentVersions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Dms\Filament\Resources\DocumentVersions\DocumentVersionResource;

class CreateDocumentVersion extends CreateRecord
{
    protected static string $resource = DocumentVersionResource::class;
}
