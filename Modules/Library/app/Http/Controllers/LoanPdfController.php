<?php

namespace Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Library\Models\Loan;
use Modules\Library\Services\LoanDocumentService;
use Symfony\Component\HttpFoundation\Response;

class LoanPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Loan $loan, LoanDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $loan,
            'library::pdf.loan',
            $service->assemble($loan),
            $service->filename($loan),
            organization: $loan->organization,
        );
    }
}
