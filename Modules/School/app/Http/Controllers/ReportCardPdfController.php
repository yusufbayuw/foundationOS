<?php

namespace Modules\School\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\School\Models\Student;
use Modules\School\Services\ReportCardDocumentService;

class ReportCardPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Request $request, Student $student, ReportCardDocumentService $service)
    {
        $periodId = (int) $request->query('period');

        abort_if($periodId <= 0, 404);

        return $this->downloadTenantPdf(
            $student,
            'school::pdf.report-card',
            $service->assemble($student, $periodId),
            $service->filename($student, $periodId),
        );
    }
}
