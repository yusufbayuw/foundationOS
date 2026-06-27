<?php

namespace Modules\Finance\Services\Support;

use Modules\Core\Models\User;
use Modules\Monitoring\Models\AuditLog;

class FinanceAuditRecorder
{
    public function record(object $record, User $actor, string $action, array $newValues = []): void
    {
        AuditLog::query()->create([
            'tenant_id' => $record->tenant_id,
            'organization_id' => $record->organization_id ?? null,
            'user_id' => $actor->getKey(),
            'auditable_type' => $record->getMorphClass(),
            'auditable_id' => $record->getKey(),
            'action' => $action,
            'description' => str($action)->headline()->toString(),
            'old_values' => null,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_id' => request()?->headers->get('X-Request-Id'),
            'status' => 'success',
            'error_message' => null,
        ]);
    }

    public function appendNotes(?string $existing, ?string $incoming): ?string
    {
        return trim(implode("\n\n", array_filter([$existing, $incoming]))) ?: null;
    }
}
