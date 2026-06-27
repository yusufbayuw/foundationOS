<?php

namespace Modules\Exam\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAuditAction;
use Modules\Exam\Models\ExamDefinition;
use Modules\Monitoring\Models\AuditLog;

class ExamAuditLogger
{
    /**
     * @param  array<string, mixed>|null  $newValues
     * @param  array<string, mixed>|null  $oldValues
     */
    public function log(
        ExamAuditAction $action,
        ExamDefinition $exam,
        string $description,
        ?User $user = null,
        ?Model $subject = null,
        ?array $newValues = null,
        ?array $oldValues = null,
        string $status = 'success',
    ): AuditLog {
        $user ??= Auth::user();

        $auditable = $subject ?? $exam;

        return AuditLog::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'user_id' => $user?->getKey(),
            'organization_id' => $exam->organization_id,
            'auditable_type' => $auditable::class,
            'auditable_id' => (string) $auditable->getKey(),
            'action' => $action->value,
            'category' => AuditLog::CATEGORY_SECURITY,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => $status,
        ]);
    }
}
