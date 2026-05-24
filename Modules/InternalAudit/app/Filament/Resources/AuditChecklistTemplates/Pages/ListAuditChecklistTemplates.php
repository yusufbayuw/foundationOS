<?php

namespace Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates\AuditChecklistTemplateResource;

class ListAuditChecklistTemplates extends ListRecords
{
    protected static string $resource = AuditChecklistTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
