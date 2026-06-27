<?php

namespace Modules\Campus\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Campus\Models\StudyPlan;
use Modules\Campus\Services\StudyPlanDocumentService;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Symfony\Component\HttpFoundation\Response;

class StudyPlanPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(StudyPlan $studyPlan, StudyPlanDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $studyPlan,
            'campus::pdf.study-plan',
            $service->assemble($studyPlan),
            $service->filename($studyPlan),
        );
    }
}
