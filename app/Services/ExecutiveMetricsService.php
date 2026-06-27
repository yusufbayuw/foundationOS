<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\Tenant;
use Modules\Employee\Models\SalarySlip;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalEntryLine;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;
use Modules\Procurement\Models\VendorBill;
use Modules\Workflow\Models\WorkflowInstance;

class ExecutiveMetricsService
{
    /**
     * @return array{
     *     revenue_mtd: float,
     *     payroll_mtd: float,
     *     outstanding_ap: float,
     *     outstanding_ar: float,
     *     active_tenants: int,
     *     pending_approvals: int
     * }
     */
    public function forTenant(int $tenantId): array
    {
        return Cache::remember(
            "executive_metrics:{$tenantId}:".now()->format('Y-m'),
            now()->addMinutes(5),
            fn (): array => [
                'revenue_mtd' => $this->revenueMtd($tenantId),
                'payroll_mtd' => $this->payrollMtd($tenantId),
                'outstanding_ap' => $this->outstandingAp($tenantId),
                'outstanding_ar' => $this->outstandingAr($tenantId),
                'active_tenants' => 1,
                'pending_approvals' => $this->pendingApprovals($tenantId),
            ],
        );
    }

    public function activeTenantsCount(): int
    {
        return Cache::remember(
            'executive_metrics:active_tenants',
            now()->addMinutes(5),
            fn (): int => (int) Tenant::query()
                ->where('status', 'active')
                ->count(),
        );
    }

    protected function revenueMtd(int $tenantId): float
    {
        return (float) Payment::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'verified')
            ->whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');
    }

    protected function payrollMtd(int $tenantId): float
    {
        return (float) SalarySlip::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['approved', 'paid'])
            ->where('period_month', (string) now()->month)
            ->where('period_year', (string) now()->year)
            ->sum('net_salary');
    }

    protected function outstandingAp(int $tenantId): float
    {
        return (float) VendorBill::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->get()
            ->sum(static fn (VendorBill $bill): float => max(
                0.0,
                (float) $bill->total_amount - (float) $bill->amount_paid,
            ));
    }

    protected function outstandingAr(int $tenantId): float
    {
        return (float) StudentInvoice::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['issued', 'partial'])
            ->sum('remaining_amount');
    }

    protected function pendingApprovals(int $tenantId): int
    {
        return (int) WorkflowInstance::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
    }

    /**
     * Revenue from posted journal credit lines (fallback when payments sparse).
     */
    public function journalRevenueMtd(int $tenantId): float
    {
        return (float) JournalEntryLine::query()
            ->where('tenant_id', $tenantId)
            ->whereHas('journalEntry', static function (Builder $query): void {
                /** @var Builder<JournalEntry> $query */
                $query->where($query->getModel()->qualifyColumn('is_posted'), true)
                    ->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month);
            })
            ->where('credit', '>', 0)
            ->sum('credit');
    }
}
