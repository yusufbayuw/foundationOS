<?php

namespace Modules\Monitoring\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Monitoring\Models\AuditLog;

trait HasAuditTrail
{
    /**
     * @return MorphMany<AuditLog, $this>
     */
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function shouldRecordAuditTrail(): bool
    {
        return true;
    }
}
