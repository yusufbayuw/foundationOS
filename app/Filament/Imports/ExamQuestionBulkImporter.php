<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Modules\Core\Support\FilamentUi;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Services\ExamQuestionBulkImportService;

class ExamQuestionBulkImporter extends Importer
{
    protected static ?string $model = ExamQuestion::class;

    public static function getColumns(): array
    {
        $columns = [
            'question_bank_uuid',
            'academic_context_type',
            'type',
            'subject',
            'grade_level',
            'course',
            'topic',
            'subtopic',
            'difficulty',
            'question_text',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'option_e',
            'correct_answer',
            'score',
            'explanation',
            'olympiad_subject',
            'olympiad_level',
            'skill_codes',
            'mi_logical',
            'mi_linguistic',
            'mi_visual',
            'mi_kinesthetic',
            'mi_interpersonal',
            'mi_intrapersonal',
            'mi_musical',
            'mi_naturalist',
        ];

        return array_map(
            fn (string $name): ImportColumn => ImportColumn::make($name)
                ->exampleHeader($name)
                ->rules(match ($name) {
                    'question_bank_uuid' => ['required', 'uuid'],
                    'academic_context_type' => ['required', 'string'],
                    'type' => ['required', 'string'],
                    'question_text' => ['required', 'string'],
                    'score' => ['nullable', 'numeric'],
                    default => ['nullable'],
                }),
            $columns,
        );
    }

    public function resolveRecord(): ?Model
    {
        return new ExamQuestion;
    }

    public function fillRecord(): void
    {
        // Row persistence is handled in saveRecord() via ExamQuestionBulkImportService.
    }

    public function saveRecord(): void
    {
        $tenantId = $this->getOptions()['tenant_id'] ?? null;

        if (! is_numeric($tenantId) || (int) $tenantId <= 0) {
            throw new RowImportFailedException(
                'Tenant context is required for tenant-scoped import.',
            );
        }

        app(ExamQuestionBulkImportService::class)->importRow($this->data, (int) $tenantId);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $successfulCount = $import->successful_rows;
        $body = FilamentUi::text('Question import completed.')
            .' '
            .Number::format($successfulCount)
            .' '
            .str(FilamentUi::text('row'))->plural($successfulCount)
            .' '
            .FilamentUi::text('imported successfully.');

        $failedCount = $import->getFailedRowsCount();

        if ($failedCount > 0) {
            $body .= ' '.Number::format($failedCount).' '.str(FilamentUi::text('row'))->plural($failedCount).' '.FilamentUi::text('failed to import. Open the import details for row-level errors.');
        }

        return $body;
    }
}
