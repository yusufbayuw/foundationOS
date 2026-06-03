<?php

namespace App\Filament\Imports;

use Modules\InternalAudit\Models\AuditProgram;

class AuditProgramImporter extends BaseModelImporter
{
    protected static ?string $model = AuditProgram::class;
}
