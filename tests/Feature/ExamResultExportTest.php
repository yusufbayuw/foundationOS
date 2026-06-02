<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamExportType;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamResult;
use Modules\Exam\Services\ExamResultExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class ExamResultExportTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_csv_export_creates_uuid_export_log(): void
    {
        $exam = $this->seedExamWithResult();
        $user = User::factory()->create();
        $this->actingAs($user);

        $export = app(ExamResultExportService::class)->exportCsv($exam);

        $this->assertInstanceOf(StreamedResponse::class, $export['response']);

        $log = $export['log'];
        $this->assertTrue(Str::isUuid($log->id));
        $this->assertSame(ExamExportType::Csv->value, $log->export_type);
        $this->assertSame(1, $log->row_count);
        $this->assertSame($user->id, $log->exported_by);
    }

    public function test_result_rows_use_result_uuid_not_token(): void
    {
        $exam = $this->seedExamWithResult();
        $rows = app(ExamResultExportService::class)->resultRows($exam);

        $this->assertCount(1, $rows);
        $this->assertTrue(Str::isUuid($rows[0]['result_id']));
        $this->assertArrayNotHasKey('token', $rows[0]);
    }

    protected function seedExamWithResult(): ExamDefinition
    {
        $tenant = $this->createExamTenant();

        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Export exam',
            'exam_academic_context' => ExamAcademicContext::School,
            'status' => ExamStatus::Published,
            'max_score' => 100,
            'runtime_exam_id' => (string) Str::uuid(),
        ]);

        $participant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $exam->id,
            'student_name' => 'Citra',
            'student_identifier' => 'S100',
        ]);

        ExamResult::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $exam->id,
            'exam_participant_id' => $participant->id,
            'runtime_result_id' => (string) Str::uuid(),
            'score' => 75,
            'max_score' => 100,
            'percentage' => 75,
            'is_passed' => true,
            'status' => 'final',
        ]);

        return $exam->refresh();
    }

    protected function createExamTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-export-'.Str::random(4),
            'name' => 'Exam Export',
            'included_modules' => ['core', 'exam'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'exam-export-'.Str::random(4),
            'name' => 'Exam Export Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }
}
