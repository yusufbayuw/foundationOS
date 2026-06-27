<?php

namespace Modules\Exam\Services;

use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\QuestionBankStatus;
use Modules\Exam\Exceptions\DuplicateExamQuestionBankCodeException;
use Modules\Exam\Models\ExamQuestionBank;

class ExamQuestionBankRegistrationService
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function register(
        int $tenantId,
        ?int $organizationId,
        string $code,
        string $name,
        ?string $description = null,
        array $metadata = [],
        ExamAcademicContext $academicContext = ExamAcademicContext::Standalone,
        ?string $standaloneSubject = null,
        QuestionBankStatus $status = QuestionBankStatus::Active,
    ): ExamQuestionBank {
        $normalizedCode = strtoupper(trim($code));

        if ($normalizedCode === '') {
            throw new \InvalidArgumentException('Exam question bank code cannot be empty.');
        }

        if ($this->codeExists($tenantId, $organizationId, $normalizedCode)) {
            throw new DuplicateExamQuestionBankCodeException($normalizedCode);
        }

        return ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenantId,
            'organization_id' => $organizationId,
            'academic_context_type' => $academicContext,
            'standalone_subject' => $standaloneSubject ?? 'General',
            'code' => $normalizedCode,
            'name' => trim($name),
            'description' => $description,
            'status' => $status,
            'metadata_json' => $metadata,
        ]);
    }

    protected function codeExists(int $tenantId, ?int $organizationId, string $code): bool
    {
        return ExamQuestionBank::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('code', $code)
            ->when(
                $organizationId !== null,
                fn ($query) => $query->where($query->getModel()->qualifyColumn('organization_id'), $organizationId),
                fn ($query) => $query->whereNull('organization_id'),
            )
            ->exists();
    }
}
