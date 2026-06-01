<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Services\PurchaseOrderDocumentService;

class PurchaseOrderPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(PurchaseOrder $purchaseOrder, PurchaseOrderDocumentService $service)
    {
        return $this->downloadTenantPdf(
            $purchaseOrder,
            'procurement::pdf.purchase-order',
            $service->assemble($purchaseOrder),
            $service->filename($purchaseOrder),
        );
    }
}
