<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Procurement\Models\RequestForQuotation;
use Modules\Procurement\Services\RequestForQuotationDocumentService;

class RequestForQuotationPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(RequestForQuotation $requestForQuotation, RequestForQuotationDocumentService $service)
    {
        return $this->downloadTenantPdf(
            $requestForQuotation,
            'procurement::pdf.request-for-quotation',
            $service->assemble($requestForQuotation),
            $service->filename($requestForQuotation),
        );
    }
}
