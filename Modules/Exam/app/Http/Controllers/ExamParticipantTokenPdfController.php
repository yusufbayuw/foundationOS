<?php

namespace Modules\Exam\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Modules\Core\Models\Tenant;
use Modules\Exam\Models\ExamDefinition;
use Symfony\Component\HttpFoundation\Response;

class ExamParticipantTokenPdfController extends Controller
{
    public function __invoke(Request $request, ExamDefinition $exam): Response
    {
        $this->authorize('view', $exam);
        $this->authorize('regenerateToken', $exam);

        $participants = $exam->examParticipants()
            ->with('activeToken')
            ->orderBy('student_name')
            ->get();

        $tenant = Tenant::find($exam->tenant_id);

        $pdf = Pdf::loadView('exam::pdf.token-cards', [
            'exam' => $exam,
            'participants' => $participants,
            'tenant' => $tenant,
        ])->setPaper('a4', 'portrait');

        $filename = sprintf(
            'ExamTokens_%s.pdf',
            str_replace(' ', '_', $exam->name ?? 'exam'),
        );

        return $pdf->download($filename);
    }
}
