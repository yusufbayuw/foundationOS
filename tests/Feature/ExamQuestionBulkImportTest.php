<?php

namespace Tests\Feature;

use App\Filament\Imports\ExamQuestionBulkImporter;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\QuestionBankStatus;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\Exam\Models\ExamQuestionOption;
use Modules\Exam\Services\ExamQuestionBulkImportService;
use Tests\TestCase;

class ExamQuestionBulkImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_import_creates_question_with_uuid_and_options(): void
    {
        [$tenant, $bank] = $this->createStandaloneBank();

        $question = app(ExamQuestionBulkImportService::class)->importRow([
            'question_bank_uuid' => $bank->id,
            'academic_context_type' => 'standalone',
            'type' => 'single_choice',
            'subject' => 'OSN Physics',
            'question_text' => '<p>Pick two</p>',
            'option_a' => '2',
            'option_b' => '3',
            'option_c' => '4',
            'correct_answer' => 'B',
            'score' => '10',
            'mi_logical' => '80',
        ], $tenant->id);

        $this->assertTrue(Str::isUuid($question->id));
        $this->assertSame($bank->id, $question->exam_question_bank_id);
        $this->assertSame(QuestionType::SingleChoice, $question->type);

        $options = ExamQuestionOption::withoutTenantScope()
            ->where('exam_question_id', $question->id)
            ->orderBy('sort_order')
            ->get();

        $this->assertCount(3, $options);
        $options->each(fn (ExamQuestionOption $option) => $this->assertTrue(Str::isUuid($option->id)));
        $this->assertTrue((bool) $options[1]->is_correct);
        $this->assertEqualsWithDelta(0.8, $question->mi_mapping_json['logical'], 0.0001);
    }

    public function test_invalid_question_bank_uuid_fails_with_clear_message(): void
    {
        [$tenant] = $this->createStandaloneBank();

        $this->expectException(RowImportFailedException::class);
        $this->expectExceptionMessage('Question bank not found');

        app(ExamQuestionBulkImportService::class)->validateRow([
            'question_bank_uuid' => (string) Str::uuid(),
            'academic_context_type' => 'standalone',
            'type' => 'single_choice',
            'question_text' => 'Hello',
            'correct_answer' => 'A',
            'option_a' => '1',
        ], $tenant->id);
    }

    public function test_invalid_correct_answer_fails(): void
    {
        [$tenant, $bank] = $this->createStandaloneBank();

        $this->expectException(RowImportFailedException::class);
        $this->expectExceptionMessage('correct_answer');

        app(ExamQuestionBulkImportService::class)->validateRow([
            'question_bank_uuid' => $bank->id,
            'academic_context_type' => 'standalone',
            'type' => 'single_choice',
            'subject' => 'OSN Physics',
            'question_text' => 'Pick one',
            'option_a' => '1',
            'option_b' => '2',
            'correct_answer' => 'Z',
        ], $tenant->id);
    }

    public function test_demo_csv_imports_twenty_questions(): void
    {
        [$tenant, $bank] = $this->createStandaloneBank();

        $path = module_path('Exam', 'resources/import/exam_question_import_demo.csv');
        $contents = str_replace('__BANK_UUID__', $bank->id, (string) file_get_contents($path));

        $handle = fopen('php://memory', 'r+');
        $this->assertNotFalse($handle);
        fwrite($handle, $contents);
        rewind($handle);

        $header = fgetcsv($handle);
        $this->assertIsArray($header);

        $service = app(ExamQuestionBulkImportService::class);
        $imported = 0;

        while (($values = fgetcsv($handle)) !== false) {
            if ($values === [null] || $values === false) {
                continue;
            }

            $row = array_combine($header, $values);

            if ($row === false) {
                continue;
            }

            $service->importRow($row, $tenant->id);
            $imported++;
        }

        fclose($handle);

        $this->assertSame(20, $imported);
        $this->assertSame(20, ExamQuestion::withoutTenantScope()->where('exam_question_bank_id', $bank->id)->count());
    }

    public function test_importer_columns_match_template_headers(): void
    {
        $columns = ExamQuestionBulkImporter::getColumns();
        $headers = array_map(fn ($column) => $column->getName(), $columns);

        $path = module_path('Exam', 'resources/import/exam_question_import_demo.csv');
        $firstLine = strtok((string) file_get_contents($path), "\n");
        $demoHeader = str_getcsv($firstLine !== false ? $firstLine : '');

        $this->assertSame($demoHeader, $headers);
    }

    public function test_importer_registers_model_for_filament_import(): void
    {
        $this->assertSame(ExamQuestion::class, ExamQuestionBulkImporter::getModel());
    }

    public function test_completed_notification_body_reports_success_and_failure_counts(): void
    {
        $import = new Import;
        $import->successful_rows = 18;
        $import->total_rows = 20;

        $body = ExamQuestionBulkImporter::getCompletedNotificationBody($import);

        $this->assertStringContainsString('18', $body);
        $this->assertStringContainsString('2', $body);
    }

    /**
     * @return array{0: Tenant, 1: ExamQuestionBank}
     */
    protected function createStandaloneBank(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-import',
            'name' => 'Exam Import',
            'included_modules' => ['core', 'exam'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'exam-import-tenant',
            'name' => 'Exam Import Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $bank = ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_context_type' => ExamAcademicContext::Standalone,
            'standalone_subject' => 'OSN Physics',
            'standalone_level' => 'Regional',
            'name' => 'OSN Prep Bank',
            'status' => QuestionBankStatus::Active,
        ]);

        return [$tenant, $bank];
    }
}
