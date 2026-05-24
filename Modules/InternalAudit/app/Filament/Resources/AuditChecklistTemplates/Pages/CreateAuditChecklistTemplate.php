<?php

namespace Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\InternalAudit\Filament\Resources\AuditChecklistTemplates\AuditChecklistTemplateResource;

class CreateAuditChecklistTemplate extends CreateRecord
{
    protected static string $resource = AuditChecklistTemplateResource::class;
}
