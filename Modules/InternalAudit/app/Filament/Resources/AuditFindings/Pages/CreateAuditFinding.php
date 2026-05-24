<?php

namespace Modules\InternalAudit\Filament\Resources\AuditFindings\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\InternalAudit\Filament\Resources\AuditFindings\AuditFindingResource;

class CreateAuditFinding extends CreateRecord
{
    protected static string $resource = AuditFindingResource::class;
}
