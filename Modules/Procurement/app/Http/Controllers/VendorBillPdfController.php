<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Procurement\Models\VendorBill;
use Modules\Procurement\Services\VendorBillDocumentService;

class VendorBillPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(VendorBill $vendorBill, VendorBillDocumentService $service)
    {
        return $this->downloadTenantPdf(
            $vendorBill,
            'procurement::pdf.vendor-bill',
            $service->assemble($vendorBill),
            $service->filename($vendorBill),
        );
    }
}
