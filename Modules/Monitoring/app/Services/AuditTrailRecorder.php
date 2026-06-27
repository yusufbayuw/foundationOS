<?php

namespace Modules\Monitoring\Services;

use Illuminate\Database\Eloquent\Model;
use Modules\Monitoring\Models\AuditLog;

class AuditTrailRecorder
{
    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public static function record(
        Model $model,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null,
    ): AuditLog {
        $tenantId = $model->getAttribute('tenant_id');

        return AuditLog::withoutTenantScope()->create([
            'tenant_id' => is_numeric($tenantId) ? (int) $tenantId : null,
            'organization_id' => $model->getAttribute('organization_id'),
            'user_id' => auth()->id(),
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'action' => $action,
            'description' => $description ?? str($action)->headline()->toString(),
            'old_values' => self::filterValues($oldValues),
            'new_values' => self::filterValues($newValues),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_id' => request()->headers->get('X-Request-Id'),
            'status' => 'success',
            'error_message' => null,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $values
     * @return array<string, mixed>|null
     */
    protected static function filterValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $filtered = collect($values)
            ->except(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])
            ->all();

        return $filtered === [] ? null : $filtered;
    }
}
