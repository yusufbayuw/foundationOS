<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Finance\Models\CustomerInvoice;
use Modules\Finance\Services\CustomerInvoiceDocumentService;
use Symfony\Component\HttpFoundation\Response;

class CustomerInvoicePdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(CustomerInvoice $customerInvoice, CustomerInvoiceDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $customerInvoice,
            'finance::pdf.customer-invoice',
            $service->assemble($customerInvoice),
            $service->filename($customerInvoice),
            organization: $customerInvoice->organization,
        );
    }
}
