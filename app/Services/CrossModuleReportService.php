<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Modules\Employee\Models\SalarySlip;
use Modules\Finance\Models\Budget;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;
use Modules\Procurement\Models\PurchaseOrder;

class CrossModuleReportService
{
    /**
     * @return array{payroll: float, revenue: float, efficiency_ratio: float|null, period: array{from: string, to: string}}
     */
    public function costEfficiency(int $tenantId, CarbonInterface $from, CarbonInterface $to): array
    {
        return Cache::remember(
            "report:cost_efficiency:{$tenantId}:{$from->toDateString()}:{$to->toDateString()}",
            now()->addMinutes(5),
            function () use ($tenantId, $from, $to): array {
                $payroll = (float) SalarySlip::query()
                    ->where('tenant_id', $tenantId)
                    ->whereIn('status', ['approved', 'paid'])
                    ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
                    ->sum('net_salary');

                $revenue = (float) Payment::query()
                    ->where('tenant_id', $tenantId)
                    ->where('status', 'verified')
                    ->whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
                    ->sum('amount');

                return [
                    'payroll' => $payroll,
                    'revenue' => $revenue,
                    'efficiency_ratio' => $revenue > 0 ? round($payroll / $revenue, 4) : null,
                    'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
                ];
            },
        );
    }

    /**
     * @return array<int, array{study_program_id: int|null, study_program_name: string, paid_amount: float, invoice_count: int}>
     */
    public function profitabilityByStudyProgram(int $tenantId, CarbonInterface $from, CarbonInterface $to): array
    {
        return Cache::remember(
            "report:profitability_sp:{$tenantId}:{$from->toDateString()}:{$to->toDateString()}",
            now()->addMinutes(5),
            function () use ($tenantId, $from, $to): array {
                $rows = StudentInvoice::query()
                    ->where('tenant_id', $tenantId)
                    ->where('status', 'paid')
                    ->whereBetween('issue_date', [$from->toDateString(), $to->toDateString()])
                    ->where('invoiceable_type', 'campus_collage_student')
                    ->with(['invoiceable.studyProgram'])
                    ->get();

                $grouped = [];
                foreach ($rows as $invoice) {
                    $student = $invoice->invoiceable;
                    $programId = $student?->study_program_id;
                    $programName = $student?->studyProgram?->name ?? 'Unassigned';
                    $key = (string) ($programId ?? 'none');

                    if (! isset($grouped[$key])) {
                        $grouped[$key] = [
                            'study_program_id' => $programId,
                            'study_program_name' => $programName,
                            'paid_amount' => 0.0,
                            'invoice_count' => 0,
                        ];
                    }

                    $grouped[$key]['paid_amount'] += (float) $invoice->paid_amount;
                    $grouped[$key]['invoice_count']++;
                }

                return array_values($grouped);
            },
        );
    }

    /**
     * @return array{allocated: float, actual: float, variance: float, variance_percent: float|null, period: array{from: string, to: string}}
     */
    public function procurementBudgetVariance(int $tenantId, CarbonInterface $from, CarbonInterface $to): array
    {
        return Cache::remember(
            "report:procurement_variance:{$tenantId}:{$from->toDateString()}:{$to->toDateString()}",
            now()->addMinutes(5),
            function () use ($tenantId, $from, $to): array {
                $allocated = (float) Budget::query()
                    ->where('tenant_id', $tenantId)
                    ->where('status', 'approved')
                    ->sum('allocated_amount');

                $actual = (float) PurchaseOrder::query()
                    ->where('tenant_id', $tenantId)
                    ->whereIn('status', ['approved', 'completed', 'received'])
                    ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
                    ->sum('total_amount');

                $variance = $allocated - $actual;

                return [
                    'allocated' => $allocated,
                    'actual' => $actual,
                    'variance' => $variance,
                    'variance_percent' => $allocated > 0 ? round(($variance / $allocated) * 100, 2) : null,
                    'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
                ];
            },
        );
    }
}
