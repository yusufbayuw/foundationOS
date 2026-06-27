<?php

namespace Modules\Exam\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAuditAction;
use Modules\Exam\Enums\ExamExportType;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamExportLog;
use Modules\Exam\Models\ExamResult;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExamResultExportService
{
    public function __construct(
        protected ExamAuditLogger $auditLogger,
    ) {}

    /**
     * @return array{response: StreamedResponse, log: ExamExportLog}
     */
    public function exportCsv(ExamDefinition $exam): array
    {
        $rows = $this->resultRows($exam);
        $filename = $this->filename($exam, 'csv');

        $response = response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, array_keys($rows[0] ?? []));

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);

        $log = $this->logExport($exam, ExamExportType::Csv, $filename, count($rows));
        $this->auditExport($exam, ExamExportType::Csv->value, $log->id, count($rows));

        return ['response' => $response, 'log' => $log];
    }

    /**
     * @return array{response: StreamedResponse, log: ExamExportLog}
     */
    public function exportExcel(ExamDefinition $exam): array
    {
        $rows = $this->resultRows($exam);
        $filename = $this->filename($exam, 'xls');

        $response = response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, array_keys($rows[0] ?? []), "\t");

            foreach ($rows as $row) {
                fputcsv($handle, $row, "\t");
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);

        $log = $this->logExport($exam, ExamExportType::Excel, $filename, count($rows));
        $this->auditExport($exam, ExamExportType::Excel->value, $log->id, count($rows));

        return ['response' => $response, 'log' => $log];
    }

    /**
     * @return array{response: Response, log: ExamExportLog}
     */
    public function exportPdf(ExamDefinition $exam)
    {
        $rows = $this->resultRows($exam);
        $filename = $this->filename($exam, 'pdf');
        $tenant = Tenant::find($exam->tenant_id);

        $pdf = Pdf::loadView('exam::pdf.results-report', [
            'exam' => $exam,
            'rows' => $rows,
            'tenant' => $tenant,
            'analytics' => app(ExamAnalyticsService::class)->build($exam),
        ])->setPaper('a4', 'portrait');

        $log = $this->logExport($exam, ExamExportType::Pdf, $filename, count($rows));
        $this->auditExport($exam, ExamExportType::Pdf->value, $log->id, count($rows));

        return [
            'response' => $pdf->download($filename),
            'log' => $log,
        ];
    }

    protected function auditExport(ExamDefinition $exam, string $type, string $exportLogId, int $rowCount): void
    {
        $this->auditLogger->log(
            ExamAuditAction::ExportResult,
            $exam,
            'Exam results exported.',
            newValues: [
                'export_type' => $type,
                'export_log_id' => $exportLogId,
                'row_count' => $rowCount,
            ],
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function resultRows(ExamDefinition $exam): array
    {
        return ExamResult::withoutTenantScope()
            ->where('exam_definition_id', $exam->id)
            ->with('examParticipant')
            ->orderByDesc('score')
            ->get()
            ->map(fn (ExamResult $result): array => [
                'result_id' => $result->id,
                'participant_name' => $result->examParticipant?->student_name,
                'participant_source' => $result->examParticipant?->participant_source?->value,
                'identifier' => $result->examParticipant?->student_identifier,
                'status' => $result->status,
                'score' => $result->score,
                'percentage' => $result->percentage,
                'passed' => $result->is_passed ? 'yes' : 'no',
                'submitted_at' => $result->submitted_at?->toIso8601String(),
                'suspicious_activity_count' => $result->suspicious_activity_count,
            ])
            ->all();
    }

    protected function filename(ExamDefinition $exam, string $extension): string
    {
        $slug = Str::slug($exam->name ?? 'exam');

        return "exam_results_{$slug}_".now()->format('Ymd_His').".{$extension}";
    }

    protected function logExport(
        ExamDefinition $exam,
        ExamExportType $type,
        string $fileName,
        int $rowCount,
    ): ExamExportLog {
        return ExamExportLog::query()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_definition_id' => $exam->id,
            'exported_by' => Auth::id(),
            'export_type' => $type->value,
            'file_name' => $fileName,
            'row_count' => $rowCount,
        ]);
    }
}
