<?php

namespace Modules\InternalAudit\Filament\Resources\AuditChecklistItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\InternalAudit\Filament\Resources\AuditChecklistItems\AuditChecklistItemResource;

class ListAuditChecklistItems extends ListRecords
{
    protected static string $resource = AuditChecklistItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
