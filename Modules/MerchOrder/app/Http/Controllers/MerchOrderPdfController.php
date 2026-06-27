<?php

namespace Modules\MerchOrder\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\MerchOrder\Models\MerchOrder;
use Modules\MerchOrder\Services\MerchOrderDocumentService;
use Symfony\Component\HttpFoundation\Response;

class MerchOrderPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(MerchOrder $merchOrder, MerchOrderDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $merchOrder,
            'merchorder::pdf.merch-order',
            $service->assemble($merchOrder),
            $service->filename($merchOrder),
            organization: $merchOrder->organization,
        );
    }
}
