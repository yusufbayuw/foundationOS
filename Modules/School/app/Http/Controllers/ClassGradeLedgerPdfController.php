<?php

namespace Modules\School\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\School\Models\SchoolClass;
use Modules\School\Services\ClassGradeLedgerService;

class ClassGradeLedgerPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(Request $request, SchoolClass $schoolClass, ClassGradeLedgerService $service)
    {
        $periodId = (int) $request->query('period');

        abort_if($periodId <= 0, 404);

        $data = $service->assemble($schoolClass, $periodId);

        return $this->downloadTenantPdf(
            $schoolClass,
            'school::pdf.grade-ledger',
            $data,
            $service->filename($schoolClass, $data['period']),
            paper: 'a4',
            orientation: 'landscape',
        );
    }
}
