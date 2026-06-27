<?php

namespace Modules\EOffice\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\EOffice\Models\Letter;
use Modules\EOffice\Services\LetterDocumentService;
use Symfony\Component\HttpFoundation\Response;

class LetterPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Letter $letter, LetterDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $letter,
            'eoffice::pdf.official-letter',
            $service->assemble($letter),
            $service->filename($letter),
            organization: $letter->organization,
        );
    }
}
