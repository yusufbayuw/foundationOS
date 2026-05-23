<?php

namespace Modules\Monitoring\Services;

use App\Support\CurrentTenant;
use Modules\Monitoring\Models\AuditLog;

class SecurityAuditLogger
{
    public function __construct(
        protected CurrentTenant $currentTenant,
    ) {}

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public function log(
        string $action,
        string $description,
        string $status = 'success',
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        $tenantId = $this->currentTenant->id();

        return AuditLog::withoutTenantScope()->create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'organization_id' => null,
            'action' => $action,
            'description' => $description,
            'category' => AuditLog::CATEGORY_SECURITY,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'status' => $status,
        ]);
    }
}
