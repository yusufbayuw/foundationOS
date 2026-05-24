<?php

namespace Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates\AuditChecklistTemplateResource;

class ViewAuditChecklistTemplate extends ViewRecord
{
    protected static string $resource = AuditChecklistTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
