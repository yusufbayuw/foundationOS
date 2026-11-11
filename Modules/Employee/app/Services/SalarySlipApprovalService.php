<?php

namespace Modules\Employee\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\User;
use Modules\Employee\Models\SalarySlip;
use Modules\Monitoring\Models\AuditLog;
use Modules\Workflow\Models\Workflow;
use RuntimeException;

class SalarySlipApprovalService
{
    public function approve(SalarySlip $slip, User $actor): SalarySlip
    {
        return DB::transaction(function () use ($slip, $actor): SalarySlip {
            $slip = SalarySlip::query()
                ->whereKey($slip->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($slip->status === 'approved') {
                return $slip;
            }

            if ($slip->status !== 'draft') {
                throw new RuntimeException('Only draft salary slips can be approved.');
            }

            $slip->load('employee');

            if (! $slip->employee || (int) $slip->employee->tenant_id !== (int) $slip->tenant_id) {
                throw new RuntimeException('Salary slip employee is not associated with this tenant.');
            }

            if ((int) $slip->employee->user_id === (int) $actor->getKey()) {
                throw new RuntimeException('Employees cannot approve their own salary slips.');
            }

            // When the HR→Finance workflow is enabled, its completed approval
            // is mandatory. Otherwise preserve the legacy direct approval flow.
            $hasActiveWorkflow = Workflow::withoutTenantScope()
                ->where('tenant_id', $slip->tenant_id)
                ->where('code', $slip->workflowCode())
                ->where('is_active', true)
                ->exists();

            if ($hasActiveWorkflow) {
                $completed = $slip->workflowInstances()
                    ->where('tenant_id', $slip->tenant_id)
                    ->where('status', 'completed')
                    ->whereHas('workflow', fn ($query) => $query
                        ->where('tenant_id', $slip->tenant_id)
                        ->where('code', $slip->workflowCode()))
                    ->exists();

                if (! $completed) {
                    throw new RuntimeException('HR and Finance workflow approval must be completed before approving a salary slip.');
                }
            }

            $slip->forceFill(['status' => 'approved'])->save();

            AuditLog::withoutTenantScope()->create([
                'tenant_id' => $slip->tenant_id,
                'organization_id' => $slip->employee->organization_id,
                'user_id' => $actor->getKey(),
                'auditable_type' => $slip->getMorphClass(),
                'auditable_id' => $slip->getKey(),
                'action' => 'payroll_salary_slip_approved',
                'description' => 'Salary slip approved after required workflow checks.',
                'old_values' => ['status' => 'draft'],
                'new_values' => ['status' => 'approved'],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'request_id' => request()?->headers->get('X-Request-Id'),
                'status' => 'success',
                'error_message' => null,
            ]);

            return $slip->fresh();
        }, 3);
    }
}
