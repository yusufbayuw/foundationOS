<?php

namespace Modules\Exam\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Services\ExamResultExportService;
use Symfony\Component\HttpFoundation\Response;

class ExamResultsPdfController extends Controller
{
    public function __invoke(Request $request, ExamDefinition $exam): Response
    {
        $this->authorize('view', $exam);
        $this->authorize('exportResult', $exam);

        return app(ExamResultExportService::class)->exportPdf($exam)['response'];
    }
}
