<?php

namespace Modules\Campus\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Campus\Models\Yudisium;
use Modules\Campus\Services\YudisiumDocumentService;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Symfony\Component\HttpFoundation\Response;

class YudisiumPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Yudisium $yudisium, YudisiumDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $yudisium,
            'campus::pdf.yudisium',
            $service->assemble($yudisium),
            $service->filename($yudisium),
        );
    }
}
