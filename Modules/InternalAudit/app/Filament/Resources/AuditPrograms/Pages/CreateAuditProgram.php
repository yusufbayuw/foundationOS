<?php

namespace Modules\InternalAudit\Filament\Resources\AuditPrograms\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\InternalAudit\Filament\Resources\AuditPrograms\AuditProgramResource;

class CreateAuditProgram extends CreateRecord
{
    protected static string $resource = AuditProgramResource::class;
}
