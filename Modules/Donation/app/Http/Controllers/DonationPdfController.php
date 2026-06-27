<?php

namespace Modules\Donation\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Donation\Models\Donation;
use Modules\Donation\Services\DonationDocumentService;
use Symfony\Component\HttpFoundation\Response;

class DonationPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Donation $donation, DonationDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $donation,
            'donation::pdf.donation-receipt',
            $service->assemble($donation),
            $service->filename($donation),
        );
    }
}
