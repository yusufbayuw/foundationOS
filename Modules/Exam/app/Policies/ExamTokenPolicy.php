<?php

declare(strict_types=1);

namespace Modules\Exam\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Exam\Enums\ExamPermission;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamToken;
use Modules\Exam\Policies\Concerns\AuthorizesExamAccess;

class ExamTokenPolicy
{
    use AuthorizesExamAccess;
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExamToken')
            && $authUser->can(ExamPermission::ManageExamToken->value);
    }

    public function view(AuthUser $authUser, ExamToken $examToken): bool
    {
        $exam = $examToken->examParticipant?->examDefinition;

        return $authUser->can('View:ExamToken')
            && $exam !== null
            && $this->canAccessExam($authUser, $exam, ExamPermission::ManageExamToken->value);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExamToken')
            && $authUser->can(ExamPermission::ManageExamToken->value);
    }

    public function update(AuthUser $authUser, ExamToken $examToken): bool
    {
        $exam = $examToken->examParticipant?->examDefinition;

        return $authUser->can('Update:ExamToken')
            && $exam !== null
            && $this->canAccessExam($authUser, $exam, ExamPermission::ManageExamToken->value);
    }

    public function delete(AuthUser $authUser, ExamToken $examToken): bool
    {
        return $this->update($authUser, $examToken);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExamToken')
            && $authUser->can(ExamPermission::ManageExamToken->value);
    }

    public function restore(AuthUser $authUser, ExamToken $examToken): bool
    {
        return $this->update($authUser, $examToken);
    }

    public function forceDelete(AuthUser $authUser, ExamToken $examToken): bool
    {
        return $this->delete($authUser, $examToken);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ExamToken')
            && $authUser->can(ExamPermission::ManageExamToken->value);
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ExamToken')
            && $authUser->can(ExamPermission::ManageExamToken->value);
    }

    public function replicate(AuthUser $authUser, ExamToken $examToken): bool
    {
        $exam = $this->examFromToken($examToken);

        return $authUser->can('Replicate:ExamToken')
            && $exam !== null
            && $this->canAccessExam($authUser, $exam, ExamPermission::ManageExamToken->value);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ExamToken')
            && $authUser->can(ExamPermission::ManageExamToken->value);
    }

    protected function examFromToken(ExamToken $examToken): ?ExamDefinition
    {
        return $examToken->examParticipant?->examDefinition;
    }
}
