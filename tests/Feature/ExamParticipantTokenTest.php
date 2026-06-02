<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamToken;
use Modules\Exam\Services\ExamTokenService;
use Tests\TestCase;

class ExamParticipantTokenTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_regenerate_deactivates_old_token_and_issues_unique_token_per_exam(): void
    {
        $exam = $this->createExam();
        $participant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_definition_id' => $exam->id,
            'student_name' => 'Token Test',
            'status' => 'assigned',
        ]);

        $service = app(ExamTokenService::class);
        $first = $service->generateToken($participant);
        $second = $service->regenerate($participant);

        $this->assertFalse($first->fresh()->is_active);
        $this->assertTrue($second->is_active);
        $this->assertNotSame($first->token, $second->token);
        $this->assertTrue($service->isUniqueForExam($exam, $second->token, $participant));

        $otherParticipant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_definition_id' => $exam->id,
            'student_name' => 'Other',
            'status' => 'assigned',
        ]);

        ExamToken::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_participant_id' => $otherParticipant->id,
            'token' => $second->token,
            'is_active' => true,
        ]);

        $this->assertFalse($service->isUniqueForExam($exam, $second->token));
    }

    public function test_participant_export_query_includes_active_token(): void
    {
        $exam = $this->createExam();
        $participant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_definition_id' => $exam->id,
            'student_name' => 'Export Me',
            'student_identifier' => 'EXP-1',
            'status' => 'assigned',
        ]);

        app(ExamTokenService::class)->generateToken($participant);

        $loaded = $exam->examParticipants()->with('activeToken')->first();

        $this->assertNotNull($loaded?->activeToken?->token);
        $this->assertSame('Export Me', $loaded->student_name);
    }

    protected function createExam(): ExamDefinition
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-b5-token',
            'name' => 'Exam B5 Token',
            'included_modules' => ['core', 'exam'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'b5-token',
            'name' => 'B5 Token Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        return ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Token Exam',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'status' => ExamStatus::Draft,
        ]);
    }
}
