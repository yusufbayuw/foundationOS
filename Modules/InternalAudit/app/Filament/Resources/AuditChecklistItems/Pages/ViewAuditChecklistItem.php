<?php

namespace Modules\InternalAudit\Filament\Resources\AuditChecklistItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\InternalAudit\Filament\Resources\AuditChecklistItems\AuditChecklistItemResource;

class ViewAuditChecklistItem extends ViewRecord
{
    protected static string $resource = AuditChecklistItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
