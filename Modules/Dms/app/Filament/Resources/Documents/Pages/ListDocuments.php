<?php

namespace Modules\Dms\Filament\Resources\Documents\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Dms\Filament\Resources\Documents\DocumentResource;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;
}
