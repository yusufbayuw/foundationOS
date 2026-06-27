<?php

namespace Modules\Campus\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Services\TranscriptDocumentService;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Symfony\Component\HttpFoundation\Response;

class CollageStudentPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(CollageStudent $collageStudent, TranscriptDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $collageStudent,
            'campus::pdf.transcript',
            $service->assemble($collageStudent),
            $service->filename($collageStudent),
        );
    }
}
