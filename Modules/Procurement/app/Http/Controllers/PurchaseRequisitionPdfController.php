<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Services\PurchaseRequisitionDocumentService;
use Symfony\Component\HttpFoundation\Response;

class PurchaseRequisitionPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(PurchaseRequisition $purchaseRequisition, PurchaseRequisitionDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $purchaseRequisition,
            'procurement::pdf.purchase-requisition',
            $service->assemble($purchaseRequisition),
            $service->filename($purchaseRequisition),
        );
    }
}
