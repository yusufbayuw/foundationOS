<?php

declare(strict_types=1);

namespace Modules\Exam\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Exam\Enums\ExamPermission;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Services\ExamAuthorizationService;

class ExamQuestionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExamQuestion')
            && $authUser->can(ExamPermission::ViewExam->value);
    }

    public function view(AuthUser $authUser, ExamQuestion $examQuestion): bool
    {
        return $authUser->can('View:ExamQuestion')
            && $authUser->can(ExamPermission::ViewExam->value)
            && app(ExamAuthorizationService::class)->examBelongsToActiveTenant($this->asExamTenantProxy($examQuestion));
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExamQuestion')
            && $authUser->can(ExamPermission::ManageQuestionBank->value);
    }

    public function update(AuthUser $authUser, ExamQuestion $examQuestion): bool
    {
        return $authUser->can('Update:ExamQuestion')
            && $authUser->can(ExamPermission::ManageQuestionBank->value)
            && app(ExamAuthorizationService::class)->examBelongsToActiveTenant($this->asExamTenantProxy($examQuestion));
    }

    public function delete(AuthUser $authUser, ExamQuestion $examQuestion): bool
    {
        return $authUser->can('Delete:ExamQuestion')
            && $authUser->can(ExamPermission::ManageQuestionBank->value)
            && app(ExamAuthorizationService::class)->examBelongsToActiveTenant($this->asExamTenantProxy($examQuestion));
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExamQuestion')
            && $authUser->can(ExamPermission::ManageQuestionBank->value);
    }

    public function restore(AuthUser $authUser, ExamQuestion $examQuestion): bool
    {
        return $this->update($authUser, $examQuestion);
    }

    public function forceDelete(AuthUser $authUser, ExamQuestion $examQuestion): bool
    {
        return $this->delete($authUser, $examQuestion);
    }

    protected function asExamTenantProxy(ExamQuestion $question): ExamDefinition
    {
        $proxy = new ExamDefinition;
        $proxy->tenant_id = $question->tenant_id;

        return $proxy;
    }
}
