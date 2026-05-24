<?php

namespace Modules\Dms\Filament\Resources\DocumentAccessLogs\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Dms\Filament\Resources\DocumentAccessLogs\DocumentAccessLogResource;

class CreateDocumentAccessLog extends CreateRecord
{
    protected static string $resource = DocumentAccessLogResource::class;
}
