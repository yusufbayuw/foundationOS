<?php

declare(strict_types=1);

namespace Modules\Exam\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Exam\Enums\ExamPermission;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\Exam\Services\ExamAuthorizationService;

class ExamQuestionBankPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExamQuestionBank')
            && $authUser->can(ExamPermission::ViewExam->value);
    }

    public function view(AuthUser $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $authUser->can('View:ExamQuestionBank')
            && $authUser->can(ExamPermission::ViewExam->value)
            && app(ExamAuthorizationService::class)->examBelongsToActiveTenant($this->asExamTenantProxy($examQuestionBank));
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExamQuestionBank')
            && $authUser->can(ExamPermission::ManageQuestionBank->value);
    }

    public function update(AuthUser $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $authUser->can('Update:ExamQuestionBank')
            && $authUser->can(ExamPermission::ManageQuestionBank->value)
            && app(ExamAuthorizationService::class)->examBelongsToActiveTenant($this->asExamTenantProxy($examQuestionBank));
    }

    public function delete(AuthUser $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $authUser->can('Delete:ExamQuestionBank')
            && $authUser->can(ExamPermission::ManageQuestionBank->value)
            && app(ExamAuthorizationService::class)->examBelongsToActiveTenant($this->asExamTenantProxy($examQuestionBank));
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExamQuestionBank')
            && $authUser->can(ExamPermission::ManageQuestionBank->value);
    }

    public function restore(AuthUser $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $this->update($authUser, $examQuestionBank);
    }

    public function forceDelete(AuthUser $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $this->delete($authUser, $examQuestionBank);
    }

    public function importQuestion(AuthUser $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $authUser->can(ExamPermission::ImportQuestion->value)
            && $this->update($authUser, $examQuestionBank);
    }

    public function generateQuestionAi(AuthUser $authUser, ExamQuestionBank $examQuestionBank): bool
    {
        return $authUser->can(ExamPermission::GenerateQuestionAi->value)
            && $this->update($authUser, $examQuestionBank);
    }

    protected function asExamTenantProxy(ExamQuestionBank $bank): ExamDefinition
    {
        $proxy = new ExamDefinition;
        $proxy->tenant_id = $bank->tenant_id;

        return $proxy;
    }
}
