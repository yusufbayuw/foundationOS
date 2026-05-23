<?php

namespace App\Services;

class ExecutiveWarningService
{
    /**
     * @param  array{cash_runway_months?: float|int, receivable_growth_percent?: float|int, room_utilization_percent?: float|int, teacher_workload_percent?: float|int}  $signals
     * @return list<array{code: string, severity: string, message: string}>
     */
    public function scanTenant(int $tenantId, array $signals = []): array
    {
        $warnings = [];

        if (($signals['cash_runway_months'] ?? 999) < 3) {
            $warnings[] = [
                'code' => 'cashflow_runway_low',
                'severity' => 'critical',
                'message' => 'Cashflow runway is below three months.',
            ];
        }

        if (($signals['receivable_growth_percent'] ?? 0) > 15) {
            $warnings[] = [
                'code' => 'receivables_spike',
                'severity' => 'high',
                'message' => 'Receivables growth exceeded the executive threshold.',
            ];
        }

        if (($signals['room_utilization_percent'] ?? 0) > 90) {
            $warnings[] = [
                'code' => 'room_overload',
                'severity' => 'high',
                'message' => 'Room utilization exceeded safe operating capacity.',
            ];
        }

        if (($signals['teacher_workload_percent'] ?? 0) > 95) {
            $warnings[] = [
                'code' => 'teacher_workload_overload',
                'severity' => 'medium',
                'message' => 'Teacher workload is above threshold.',
            ];
        }

        return $warnings;
    }
}
