<?php

namespace App\Filament\Imports;

use Modules\Monitoring\Models\AuditLog;

class AuditLogImporter extends BaseModelImporter
{
    protected static ?string $model = AuditLog::class;
}
