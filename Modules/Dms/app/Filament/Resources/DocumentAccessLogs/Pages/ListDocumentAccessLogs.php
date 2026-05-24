<?php

namespace Modules\Dms\Filament\Resources\DocumentAccessLogs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Dms\Filament\Resources\DocumentAccessLogs\DocumentAccessLogResource;

class ListDocumentAccessLogs extends ListRecords
{
    protected static string $resource = DocumentAccessLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
