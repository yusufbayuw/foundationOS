<?php

namespace App\Listeners;

use App\Events\TenantSwitched;
use Modules\Monitoring\Models\AuditLog;

class LogTenantSwitchAudit
{
    public function handle(TenantSwitched $event): void
    {
        AuditLog::withoutTenantScope()->create([
            'tenant_id' => $event->newTenantId,
            'user_id' => $event->userId,
            'action' => 'tenant_switch',
            'description' => "User switched to tenant {$event->newTenantId}",
            'old_values' => ['tenant_id' => $event->previousTenantId],
            'new_values' => ['tenant_id' => $event->newTenantId],
            'ip_address' => request()->ip(),
            'status' => 'success',
        ]);
    }
}
