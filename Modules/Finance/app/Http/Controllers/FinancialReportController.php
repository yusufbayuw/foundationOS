<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Models\Tenant;
use Modules\Finance\Services\FinancialReportService;

class FinancialReportController extends Controller
{
    public function __construct(private readonly FinancialReportService $service) {}

    public function profitLossPdf(Request $request)
    {
        $tenantId = (int) $request->query('tenant_id', filament()->getTenant()?->getKey() ?? 0);
        $tenant = Tenant::findOrFail($tenantId);
        $from = Carbon::parse($request->query('from', now()->startOfYear()->toDateString()));
        $to = Carbon::parse($request->query('to', now()->toDateString()));
        $organizationId = $request->query('organization_id') ? (int) $request->query('organization_id') : null;

        $data = $this->service->profitAndLoss($tenantId, $from, $to, $organizationId);

        $pdf = Pdf::loadView('finance::pdf.profit-loss', ['data' => $data, 'tenant' => $tenant])
            ->setPaper('a4', 'portrait');

        return $pdf->download("Laba_Rugi_{$data['period_from']}_{$data['period_to']}.pdf");
    }

    public function balanceSheetPdf(Request $request)
    {
        $tenantId = (int) $request->query('tenant_id', filament()->getTenant()?->getKey() ?? 0);
        $tenant = Tenant::findOrFail($tenantId);
        $asOf = Carbon::parse($request->query('as_of', now()->toDateString()));
        $organizationId = $request->query('organization_id') ? (int) $request->query('organization_id') : null;

        $data = $this->service->balanceSheet($tenantId, $asOf, $organizationId);

        $pdf = Pdf::loadView('finance::pdf.balance-sheet', ['data' => $data, 'tenant' => $tenant])
            ->setPaper('a4', 'portrait');

        return $pdf->download("Neraca_{$data['as_of']}.pdf");
    }

    public function cashFlowPdf(Request $request)
    {
        $tenantId = (int) $request->query('tenant_id', filament()->getTenant()?->getKey() ?? 0);
        $tenant = Tenant::findOrFail($tenantId);
        $from = Carbon::parse($request->query('from', now()->startOfYear()->toDateString()));
        $to = Carbon::parse($request->query('to', now()->toDateString()));
        $organizationId = $request->query('organization_id') ? (int) $request->query('organization_id') : null;

        $data = $this->service->cashFlow($tenantId, $from, $to, $organizationId);

        $pdf = Pdf::loadView('finance::pdf.cash-flow', ['data' => $data, 'tenant' => $tenant])
            ->setPaper('a4', 'portrait');

        return $pdf->download("Arus_Kas_{$data['period_from']}_{$data['period_to']}.pdf");
    }
}
