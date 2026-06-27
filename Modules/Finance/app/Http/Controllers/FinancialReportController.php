<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\TypedValue;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Core\Models\Tenant;
use Modules\Finance\Services\FinancialReportService;
use Symfony\Component\HttpFoundation\Response;

class FinancialReportController extends Controller
{
    public function __construct(private readonly FinancialReportService $service) {}

    public function profitLossPdf(Request $request): Response
    {
        $defaultTenantId = TypedValue::int(filament()->getTenant()?->getKey());
        $tenantId = TypedValue::int($request->query('tenant_id'), $defaultTenantId);
        $tenant = Tenant::findOrFail($tenantId);
        $from = Carbon::parse($request->query('from', now()->startOfYear()->toDateString()));
        $to = Carbon::parse($request->query('to', now()->toDateString()));
        $organizationId = $request->query('organization_id') ? (int) $request->query('organization_id') : null;

        $data = $this->service->profitAndLoss($tenantId, $from, $to, $organizationId);

        $pdf = Pdf::loadView('finance::pdf.profit-loss', ['data' => $data, 'tenant' => $tenant])
            ->setPaper('a4', 'portrait');

        return $pdf->download(sprintf(
            'Laba_Rugi_%s_%s.pdf',
            TypedValue::string($data['period_from'] ?? ''),
            TypedValue::string($data['period_to'] ?? ''),
        ));
    }

    public function balanceSheetPdf(Request $request): Response
    {
        $defaultTenantId = TypedValue::int(filament()->getTenant()?->getKey());
        $tenantId = TypedValue::int($request->query('tenant_id'), $defaultTenantId);
        $tenant = Tenant::findOrFail($tenantId);
        $asOf = Carbon::parse($request->query('as_of', now()->toDateString()));
        $organizationId = $request->query('organization_id') ? (int) $request->query('organization_id') : null;

        $data = $this->service->balanceSheet($tenantId, $asOf, $organizationId);

        $pdf = Pdf::loadView('finance::pdf.balance-sheet', ['data' => $data, 'tenant' => $tenant])
            ->setPaper('a4', 'portrait');

        return $pdf->download('Neraca_'.TypedValue::string($data['as_of'] ?? '').'.pdf');
    }

    public function cashFlowPdf(Request $request): Response
    {
        $defaultTenantId = TypedValue::int(filament()->getTenant()?->getKey());
        $tenantId = TypedValue::int($request->query('tenant_id'), $defaultTenantId);
        $tenant = Tenant::findOrFail($tenantId);
        $from = Carbon::parse($request->query('from', now()->startOfYear()->toDateString()));
        $to = Carbon::parse($request->query('to', now()->toDateString()));
        $organizationId = $request->query('organization_id') ? (int) $request->query('organization_id') : null;

        $data = $this->service->cashFlow($tenantId, $from, $to, $organizationId);

        $pdf = Pdf::loadView('finance::pdf.cash-flow', ['data' => $data, 'tenant' => $tenant])
            ->setPaper('a4', 'portrait');

        return $pdf->download(sprintf(
            'Arus_Kas_%s_%s.pdf',
            TypedValue::string($data['period_from'] ?? ''),
            TypedValue::string($data['period_to'] ?? ''),
        ));
    }
}
