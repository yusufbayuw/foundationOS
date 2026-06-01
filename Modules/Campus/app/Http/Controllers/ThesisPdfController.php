<?php

namespace Modules\Campus\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Campus\Models\Thesis;
use Modules\Campus\Services\ThesisDocumentService;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;

class ThesisPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Thesis $thesis, ThesisDocumentService $service)
    {
        return $this->downloadTenantPdf(
            $thesis,
            'campus::pdf.thesis-letter',
            $service->assemble($thesis),
            $service->filename($thesis),
        );
    }
}
