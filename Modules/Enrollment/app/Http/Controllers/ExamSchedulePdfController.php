<?php

namespace Modules\Enrollment\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Enrollment\Models\ExamSchedule;
use Modules\Enrollment\Services\ExamScheduleDocumentService;

class ExamSchedulePdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(ExamSchedule $examSchedule, ExamScheduleDocumentService $service)
    {
        return $this->downloadTenantPdf(
            $examSchedule,
            'enrollment::pdf.exam-schedule-card',
            $service->assemble($examSchedule),
            $service->filename($examSchedule),
        );
    }
}
