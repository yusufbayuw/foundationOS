<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Services\ExamContextResolver;
use Modules\School\Models\Student;
use Tests\TestCase;

class ExamContextHybridMappingTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_context_resolver_reads_hybrid_student_reference(): void
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-school',
            'name' => 'Exam School',
            'included_modules' => ['core', 'school', 'exam'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'school-exam',
            'name' => 'School Exam Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $student = Student::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'nis' => '10001',
            'nisn' => '0012345678',
        ]);

        $definition = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Class Quiz',
            'exam_academic_context' => ExamAcademicContext::School,
            'context_reference_type' => Student::class,
            'context_reference_id' => $student->id,
            'status' => ExamStatus::Draft,
        ]);

        $resolved = app(ExamContextResolver::class)->resolveDefinitionContext($definition);

        $this->assertSame('school', $resolved['exam_academic_context']);
        $this->assertSame(Student::class, $resolved['context_reference_type']);
        $this->assertSame($student->id, $resolved['context_reference_id']);
        $this->assertSame('10001', $resolved['resolved_label']);
    }
}
