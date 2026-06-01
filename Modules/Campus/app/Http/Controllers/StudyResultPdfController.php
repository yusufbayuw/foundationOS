<?php

namespace Modules\Campus\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Campus\Models\StudyResult;
use Modules\Campus\Services\StudyResultDocumentService;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;

class StudyResultPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(StudyResult $studyResult, StudyResultDocumentService $service)
    {
        return $this->downloadTenantPdf(
            $studyResult,
            'campus::pdf.study-result',
            $service->assemble($studyResult),
            $service->filename($studyResult),
        );
    }
}
