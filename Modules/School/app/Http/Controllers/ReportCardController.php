<?php

namespace Modules\School\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Modules\School\Models\Student;
use Modules\School\Services\ReportCardService;

class ReportCardController extends Controller
{
    public function download(Request $request, ReportCardService $service)
    {
        $studentId = $request->query('student');
        $periodId = $request->query('period');

        if (! $studentId || ! $periodId) {
            abort(404);
        }

        // The legacy download URL must enforce the same Student print
        // authorization as the newer route-model-bound PDF endpoint.
        $student = Student::query()->findOrFail($studentId);
        $this->authorize('print', $student);

        $data = $service->generate($student->getKey(), (int) $periodId);

        $pdf = Pdf::loadView('school::report-card-pdf', ['data' => $data]);

        $studentName = $data['student']->user->name ?? 'Student';

        return $pdf->download('Rapor_'.str_replace(' ', '_', $studentName).'.pdf');
    }
}
