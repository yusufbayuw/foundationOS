<?php

namespace Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Library\Models\Fine;
use Modules\Library\Services\FineDocumentService;

class FinePdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Fine $fine, FineDocumentService $service)
    {
        return $this->downloadTenantPdf(
            $fine,
            'library::pdf.fine',
            $service->assemble($fine),
            $service->filename($fine),
            organization: $fine->organization,
        );
    }
}
