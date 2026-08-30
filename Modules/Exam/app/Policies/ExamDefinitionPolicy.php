<?php

declare(strict_types=1);

namespace Modules\Exam\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Exam\Enums\ExamPermission;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Policies\Concerns\AuthorizesExamAccess;
use Modules\Exam\Services\ExamAuthorizationService;

class ExamDefinitionPolicy
{
    use AuthorizesExamAccess;
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return app(ExamAuthorizationService::class)->canViewAny($authUser);
    }

    public function view(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $authUser->can('View:ExamDefinition')
            && $this->canViewExam($authUser, $examDefinition);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExamDefinition')
            && $authUser->can(ExamPermission::CreateExam->value);
    }

    public function update(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $authUser->can('Update:ExamDefinition')
            && $this->canAccessExam($authUser, $examDefinition, ExamPermission::UpdateExam->value);
    }

    public function delete(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $authUser->can('Delete:ExamDefinition')
            && $this->canAccessExam($authUser, $examDefinition, ExamPermission::DeleteExam->value);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExamDefinition')
            && $authUser->can(ExamPermission::DeleteExam->value);
    }

    public function restore(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $authUser->can('Restore:ExamDefinition')
            && $this->canAccessExam($authUser, $examDefinition, ExamPermission::UpdateExam->value);
    }

    public function forceDelete(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $authUser->can('ForceDelete:ExamDefinition')
            && $this->canAccessExam($authUser, $examDefinition, ExamPermission::DeleteExam->value);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ExamDefinition')
            && $authUser->can(ExamPermission::DeleteExam->value);
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ExamDefinition')
            && $authUser->can(ExamPermission::UpdateExam->value);
    }

    public function replicate(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $authUser->can('Replicate:ExamDefinition')
            && $this->canAccessExam($authUser, $examDefinition, ExamPermission::CreateExam->value);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ExamDefinition')
            && $authUser->can(ExamPermission::UpdateExam->value);
    }

    public function publish(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $this->canAccessExam($authUser, $examDefinition, ExamPermission::PublishExam->value);
    }

    public function republish(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $this->publish($authUser, $examDefinition);
    }

    public function syncParticipants(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $this->canAccessExam($authUser, $examDefinition, ExamPermission::UpdateExam->value);
    }

    public function syncResults(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $this->canAccessExam($authUser, $examDefinition, ExamPermission::SyncExamResult->value);
    }

    public function gradeAnswer(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $this->canAccessExam($authUser, $examDefinition, ExamPermission::GradeExamAnswer->value);
    }

    public function exportResult(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $this->canAccessExam($authUser, $examDefinition, ExamPermission::ExportExamResult->value);
    }

    public function regenerateToken(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $this->canAccessExam($authUser, $examDefinition, ExamPermission::ManageExamToken->value);
    }

    public function generateQuestionAi(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $this->canAccessExam($authUser, $examDefinition, ExamPermission::GenerateQuestionAi->value);
    }

    public function openControlRoom(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return app(ExamAuthorizationService::class)->canOpenControlRoom($authUser, $examDefinition);
    }

    public function pushToSchoolGradebook(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $examDefinition->isSchool()
            && $this->canAccessExam($authUser, $examDefinition, ExamPermission::PushExamGradebook->value);
    }

    public function pushToCampusGradebook(AuthUser $authUser, ExamDefinition $examDefinition): bool
    {
        return $examDefinition->isCampus()
            && $this->canAccessExam($authUser, $examDefinition, ExamPermission::PushExamGradebook->value);
    }
}
