<?php

namespace Modules\School\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\Core\Models\Tenant;
use Modules\Core\Support\Pdf\PdfDocumentRenderer;
use Modules\Core\Support\Pdf\TenantDocumentContext;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Services\ReportCardDocumentService;

class ReportCardBulkPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(
        Request $request,
        SchoolClass $schoolClass,
        ReportCardDocumentService $service,
    ) {
        $periodId = (int) $request->query('period');

        abort_if($periodId <= 0, 404);

        $this->authorizePrint($schoolClass);

        $studentIds = ClassStudent::query()
            ->where('class_id', $schoolClass->getKey())
            ->where('academic_period_id', $periodId)
            ->pluck('student_id');

        abort_if($studentIds->isEmpty(), 404);

        $tenant = Tenant::query()->findOrFail($schoolClass->tenant_id);
        $context = TenantDocumentContext::resolve($tenant, $schoolClass->organization);

        $documents = [];
        foreach ($studentIds as $studentId) {
            $student = Student::query()->find($studentId);
            if ($student === null) {
                continue;
            }

            $documents[] = [
                'view' => 'school::pdf.report-card',
                'data' => $service->assemble($student, $periodId),
                'filename' => $service->filename($student, $periodId),
            ];
        }

        abort_if($documents === [], 404);

        $zipName = sprintf(
            'Rapor_Massal_%s.pdf',
            str_replace(' ', '_', $schoolClass->name ?? 'Kelas'),
        );

        return app(PdfDocumentRenderer::class)->downloadZip($documents, $context, $zipName);
    }
}
