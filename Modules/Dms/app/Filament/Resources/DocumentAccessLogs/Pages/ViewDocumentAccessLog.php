<?php

namespace Modules\Dms\Filament\Resources\DocumentAccessLogs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Dms\Filament\Resources\DocumentAccessLogs\DocumentAccessLogResource;

class ViewDocumentAccessLog extends ViewRecord
{
    protected static string $resource = DocumentAccessLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
