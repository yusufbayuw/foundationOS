<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Services\SalesOrderDocumentService;
use Symfony\Component\HttpFoundation\Response;

class SalesOrderPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(SalesOrder $salesOrder, SalesOrderDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $salesOrder,
            'sales::pdf.sales-order',
            $service->assemble($salesOrder),
            $service->filename($salesOrder),
        );
    }
}
