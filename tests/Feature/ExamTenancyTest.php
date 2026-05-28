<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Models\ExamDefinition;
use Tests\TestCase;

class ExamTenancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_exam_definition_uses_uuid_primary_key_and_tenant_scope(): void
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-starter',
            'name' => 'Exam Starter',
            'included_modules' => ['core', 'exam'],
        ]);

        $tenantA = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-a-exam',
            'name' => 'Tenant A Exam',
            'subscription_plan_id' => $plan->id,
        ]);

        $tenantB = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-b-exam',
            'name' => 'Tenant B Exam',
            'subscription_plan_id' => $plan->id,
        ]);

        $examA = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenantA->id,
            'name' => 'Midterm A',
            'code' => 'MID-A',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'status' => ExamStatus::Draft,
        ]);

        ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenantB->id,
            'name' => 'Midterm B',
            'code' => 'MID-B',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'status' => ExamStatus::Draft,
        ]);

        $this->assertTrue(Str::isUuid($examA->id));

        app(CurrentTenant::class)->set($tenantA);

        $this->assertCount(1, ExamDefinition::query()->get());
        $this->assertSame('Midterm A', ExamDefinition::query()->first()?->name);
    }
}
