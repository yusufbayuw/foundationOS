<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Procurement\Models\VendorBill;
use Modules\Procurement\Services\VendorBillDocumentService;
use Symfony\Component\HttpFoundation\Response;

class VendorBillPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(VendorBill $vendorBill, VendorBillDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $vendorBill,
            'procurement::pdf.vendor-bill',
            $service->assemble($vendorBill),
            $service->filename($vendorBill),
        );
    }
}
