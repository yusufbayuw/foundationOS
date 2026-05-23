<?php

namespace Modules\Legal\Filament\Resources\LegalDocuments\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Legal\Filament\Resources\LegalDocuments\LegalDocumentResource;

class ListLegalDocuments extends ListRecords
{
    protected static string $resource = LegalDocumentResource::class;
}
