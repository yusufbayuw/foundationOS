<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamResult;
use Modules\Exam\Services\ExamAnalyticsService;
use Tests\TestCase;

class ExamAnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_analytics_includes_class_sections(): void
    {
        $exam = $this->seedExam(ExamAcademicContext::School, [
            'standalone_subject' => null,
        ]);

        $analytics = app(ExamAnalyticsService::class)->build($exam);

        $this->assertSame('school', $analytics['context']);
        $keys = array_column($analytics['sections'], 'key');

        $this->assertContains('class_overview', $keys);
        $this->assertContains('per_student', $keys);
        $this->assertContains('remedial', $keys);
    }

    public function test_standalone_analytics_includes_ranking_and_readiness(): void
    {
        $exam = $this->seedExam(ExamAcademicContext::Standalone, [
            'standalone_subject' => 'Physics',
            'standalone_level' => 'OSN',
        ]);

        $analytics = app(ExamAnalyticsService::class)->build($exam);
        $keys = array_column($analytics['sections'], 'key');

        $this->assertSame('standalone', $analytics['context']);
        $this->assertContains('ranking', $keys);
        $this->assertContains('readiness', $keys);
        $this->assertContains('weak_topics', $keys);
        $this->assertContains('tryout_summary', $keys);
    }

    protected function seedExam(ExamAcademicContext $context, array $extra = []): ExamDefinition
    {
        $tenant = $this->createExamTenant();

        $exam = ExamDefinition::withoutTenantScope()->create(array_merge([
            'tenant_id' => $tenant->id,
            'name' => 'Analytics exam',
            'exam_academic_context' => $context,
            'status' => ExamStatus::Published,
            'max_score' => 100,
            'passing_score' => 60,
            'runtime_exam_id' => (string) Str::uuid(),
        ], $extra));

        $participant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $exam->id,
            'student_name' => 'Ana',
            'student_identifier' => 'X1',
        ]);

        ExamResult::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $exam->id,
            'exam_participant_id' => $participant->id,
            'runtime_result_id' => (string) Str::uuid(),
            'score' => 90,
            'max_score' => 100,
            'percentage' => 90,
            'is_passed' => true,
            'status' => 'final',
        ]);

        return $exam->refresh();
    }

    protected function createExamTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-analytics-'.Str::random(4),
            'name' => 'Exam Analytics',
            'included_modules' => ['core', 'exam'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'exam-analytics-'.Str::random(4),
            'name' => 'Exam Analytics Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }
}
