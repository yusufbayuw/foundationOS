<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Finance\Models\Payment;
use Modules\Finance\Services\PaymentDocumentService;
use Symfony\Component\HttpFoundation\Response;

class PaymentPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Payment $payment, PaymentDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $payment,
            'finance::pdf.payment-receipt',
            $service->assemble($payment),
            $service->filename($payment),
        );
    }
}
