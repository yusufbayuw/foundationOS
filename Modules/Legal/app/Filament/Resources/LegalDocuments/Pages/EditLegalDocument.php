<?php

namespace Modules\Legal\Filament\Resources\LegalDocuments\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Legal\Filament\Resources\LegalDocuments\LegalDocumentResource;

class EditLegalDocument extends EditRecord
{
    protected static string $resource = LegalDocumentResource::class;
}
