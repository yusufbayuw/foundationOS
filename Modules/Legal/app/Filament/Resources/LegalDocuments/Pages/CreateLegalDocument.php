<?php

namespace Modules\Legal\Filament\Resources\LegalDocuments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Legal\Filament\Resources\LegalDocuments\LegalDocumentResource;

class CreateLegalDocument extends CreateRecord
{
    protected static string $resource = LegalDocumentResource::class;
}
