<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Services\ExamParticipantResolver;
use Tests\TestCase;

class ExamParticipantManualImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_import_creates_participants_with_tokens(): void
    {
        $exam = $this->createStandaloneExam();

        $result = app(ExamParticipantResolver::class)->importManualParticipants($exam, [
            ['student_name' => 'Manual One', 'student_identifier' => 'M-001', 'email' => 'one@test.com'],
            ['student_name' => 'Manual Two', 'student_identifier' => 'M-002'],
        ]);

        $this->assertSame(2, $result['created']);
        $this->assertCount(2, ExamParticipant::withoutTenantScope()->get());

        $first = ExamParticipant::withoutTenantScope()->where('student_identifier', 'M-001')->first();
        $this->assertNotNull($first);
        $this->assertSame(ParticipantSource::Manual, $first->participant_source);
        $this->assertNotNull($first->activeToken);
    }

    public function test_csv_import_skips_duplicate_identifiers(): void
    {
        $exam = $this->createStandaloneExam();

        app(ExamParticipantResolver::class)->importFromCsvRows($exam, [
            ['student_name' => 'CSV One', 'student_identifier' => 'CSV-1'],
        ]);

        $result = app(ExamParticipantResolver::class)->importFromCsvRows($exam, [
            ['student_name' => 'CSV Duplicate', 'student_identifier' => 'CSV-1'],
        ]);

        $this->assertSame(0, $result['created']);
        $this->assertSame(1, $result['skipped']);
        $this->assertSame(1, ExamParticipant::withoutTenantScope()->where('student_identifier', 'CSV-1')->count());
    }

    protected function createStandaloneExam(): ExamDefinition
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-b5-manual',
            'name' => 'Exam B5 Manual',
            'included_modules' => ['core', 'exam'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'b5-manual',
            'name' => 'B5 Manual Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        return ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Standalone Tryout',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'status' => ExamStatus::Draft,
        ]);
    }
}
