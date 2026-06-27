<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Services\PurchaseOrderDocumentService;
use Symfony\Component\HttpFoundation\Response;

class PurchaseOrderPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(PurchaseOrder $purchaseOrder, PurchaseOrderDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $purchaseOrder,
            'procurement::pdf.purchase-order',
            $service->assemble($purchaseOrder),
            $service->filename($purchaseOrder),
        );
    }
}
