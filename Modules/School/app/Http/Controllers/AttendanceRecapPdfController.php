<?php

namespace Modules\School\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\School\Models\SchoolClass;
use Modules\School\Services\AttendanceRecapDocumentService;
use Symfony\Component\HttpFoundation\Response;

class AttendanceRecapPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Request $request, SchoolClass $schoolClass, AttendanceRecapDocumentService $service): Response
    {
        $periodId = (int) $request->query('period');
        $month = (int) $request->query('month');
        $year = (int) $request->query('year');

        abort_if($periodId <= 0 || $month <= 0 || $year <= 0, 404);

        return $this->downloadTenantPdf(
            $schoolClass,
            'school::pdf.attendance-recap',
            $service->assemble($schoolClass, $periodId, $month, $year),
            $service->filename($schoolClass, $month, $year),
        );
    }
}
