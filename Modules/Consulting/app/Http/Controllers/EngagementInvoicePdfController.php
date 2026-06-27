<?php

namespace Modules\Consulting\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Consulting\Models\EngagementInvoice;
use Modules\Consulting\Services\EngagementInvoiceDocumentService;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Symfony\Component\HttpFoundation\Response;

class EngagementInvoicePdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(EngagementInvoice $engagementInvoice, EngagementInvoiceDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $engagementInvoice,
            'consulting::pdf.engagement-invoice',
            $service->assemble($engagementInvoice),
            $service->filename($engagementInvoice),
            organization: $engagementInvoice->organization,
        );
    }
}
