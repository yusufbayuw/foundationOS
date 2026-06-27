<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Finance\Models\StudentInvoice;
use Modules\School\Models\Attendance;
use Modules\School\Models\Student;
use Modules\School\Models\StudentGrade;

class StudentDashboardController extends ApiController
{
    private const CACHE_TTL_SECONDS = 300; // 5 minutes

    public function show(Request $request, int $id): JsonResponse
    {
        $student = Student::query()->findOrFail($id);

        $cacheKey = "student_dashboard:{$id}:".now()->format('YmdHi');

        $data = Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, fn () => $this->buildDashboard($student));

        return response()->json(['data' => $data]);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDashboard(Student $student): array
    {
        // Recent attendance (last 30 days)
        $attendance = Attendance::where('student_id', $student->id)
            ->where('attendance_date', '>=', now()->subDays(30))
            ->orderByDesc('attendance_date')
            ->limit(30)
            ->get(['attendance_date', 'status', 'entry_method'])
            ->map(fn ($a) => [
                'date' => $a->attendance_date->toDateString(),
                'status' => $a->status,
                'method' => $a->entry_method,
            ]);

        // Attendance summary
        $attendanceSummary = [
            'present' => $attendance->where('status', 'present')->count(),
            'absent' => $attendance->where('status', 'absent')->count(),
            'late' => $attendance->where('status', 'late')->count(),
            'sick' => $attendance->where('status', 'sick')->count(),
        ];

        // Recent grades
        $grades = StudentGrade::where('student_id', $student->id)
            ->orderByDesc('graded_at')
            ->limit(10)
            ->get(['score', 'score_letter', 'final_score', 'is_passed', 'graded_at'])
            ->map(fn ($g) => [
                'score' => $g->score,
                'score_letter' => $g->score_letter,
                'final_score' => $g->final_score,
                'is_passed' => $g->is_passed,
                'graded_at' => $g->graded_at?->toDateString(),
            ]);

        // Outstanding fees
        $outstandingInvoices = StudentInvoice::withoutTenantScope()
            ->where('invoiceable_type', Student::class)
            ->where('invoiceable_id', $student->id)
            ->whereIn('status', ['draft', 'sent', 'overdue'])
            ->where('remaining_amount', '>', 0)
            ->orderBy('due_date')
            ->limit(5)
            ->get(['invoice_number', 'due_date', 'total_amount', 'remaining_amount', 'status'])
            ->map(fn ($inv) => [
                'invoice_number' => $inv->invoice_number,
                'due_date' => $inv->due_date->toDateString(),
                'total_amount' => (float) $inv->total_amount,
                'remaining_amount' => (float) $inv->remaining_amount,
                'status' => $inv->status,
            ]);

        return [
            'student' => [
                'id' => $student->id,
                'nis' => $student->nis,
                'status' => $student->status,
            ],
            'attendance' => [
                'summary' => $attendanceSummary,
                'recent' => $attendance->values(),
            ],
            'grades' => [
                'recent' => $grades->values(),
            ],
            'fees' => [
                'outstanding' => $outstandingInvoices->values(),
                'total_outstanding' => $outstandingInvoices->sum('remaining_amount'),
            ],
            'generated_at' => now()->toIso8601String(),
        ];
    }
}
