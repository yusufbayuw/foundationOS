<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\QuestionBankStatus;
use Modules\Exam\Exceptions\DuplicateExamQuestionBankCodeException;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\Exam\Services\ExamQuestionBankRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class ExamQuestionBankRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_standalone_bank_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'exam']);

        $bank = app(ExamQuestionBankRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' math ',
            name: 'Mathematics Pool',
            standaloneSubject: 'Mathematics',
        );

        $this->assertSame('MATH', $bank->code);
        $this->assertSame(ExamAcademicContext::Standalone, $bank->academic_context_type);
        $this->assertSame(QuestionBankStatus::Active, $bank->status);
        $this->assertDatabaseHas(ExamQuestionBank::class, [
            'id' => $bank->id,
            'code' => 'MATH',
        ]);
    }

    public function test_register_rejects_duplicate_code_in_same_scope(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'exam']);

        $service = app(ExamQuestionBankRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'SCI', 'Science');

        $this->expectException(DuplicateExamQuestionBankCodeException::class);
        $service->register($tenant->id, $organization->id, 'sci', 'Science duplicate');
    }
}
