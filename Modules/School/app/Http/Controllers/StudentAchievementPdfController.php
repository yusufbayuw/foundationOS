<?php

namespace Modules\School\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Http\Controllers\Concerns\RendersTenantPdf;
use Modules\School\Models\StudentAchievement;
use Modules\School\Services\StudentAchievementDocumentService;
use Symfony\Component\HttpFoundation\Response;

class StudentAchievementPdfController extends Controller
{
    use RendersTenantPdf;

    public function __invoke(StudentAchievement $studentAchievement, StudentAchievementDocumentService $service): Response
    {
        return $this->downloadTenantPdf(
            $studentAchievement,
            'school::pdf.student-achievement',
            $service->assemble($studentAchievement),
            $service->filename($studentAchievement),
        );
    }
}
