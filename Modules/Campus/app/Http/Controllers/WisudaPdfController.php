<?php

namespace Modules\Campus\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Campus\Models\Wisuda;
use Modules\Campus\Services\WisudaDocumentService;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;

class WisudaPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Wisuda $wisuda, WisudaDocumentService $service)
    {
        return $this->downloadTenantPdf(
            $wisuda,
            'campus::pdf.wisuda',
            $service->assemble($wisuda),
            $service->filename($wisuda),
        );
    }
}
