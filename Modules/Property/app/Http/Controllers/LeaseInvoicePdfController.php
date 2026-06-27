<?php

namespace Modules\Property\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Property\Models\LeaseInvoice;
use Modules\Property\Services\LeaseInvoiceDocumentService;
use Symfony\Component\HttpFoundation\Response;

class LeaseInvoicePdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(LeaseInvoice $leaseInvoice, LeaseInvoiceDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $leaseInvoice,
            'property::pdf.lease-invoice',
            $service->assemble($leaseInvoice),
            $service->filename($leaseInvoice),
            organization: $leaseInvoice->organization,
        );
    }
}
