<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Modules\Core\Models\Tenant;
use Modules\Employee\Models\SalarySlip;

class SalarySlipController extends Controller
{
    public function download(Request $request, SalarySlip $salarySlip)
    {
        $this->authorize('view', $salarySlip);

        $salarySlip->load(['employee.position', 'employee.department', 'employee.organization']);

        $tenant = Tenant::find($salarySlip->tenant_id);

        $pdf = Pdf::loadView('employee::pdf.salary-slip', [
            'slip' => $salarySlip,
            'tenant' => $tenant,
        ])->setPaper('a4', 'portrait');

        $filename = sprintf(
            'SlipGaji_%s_%s.pdf',
            str_replace(' ', '_', $salarySlip->employee->full_name ?? 'Karyawan'),
            str_replace(' ', '_', $salarySlip->period_label ?? ''),
        );

        return $pdf->download($filename);
    }
}
