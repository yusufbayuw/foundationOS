<?php

namespace Modules\InternalAudit\Filament\Resources\AuditChecklistItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\InternalAudit\Filament\Resources\AuditChecklistItems\AuditChecklistItemResource;

class CreateAuditChecklistItem extends CreateRecord
{
    protected static string $resource = AuditChecklistItemResource::class;
}
