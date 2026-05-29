<?php

declare(strict_types=1);

namespace Modules\Exam\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Exam\Enums\ExamPermission;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Policies\Concerns\AuthorizesExamAccess;

class ExamParticipantPolicy
{
    use AuthorizesExamAccess;
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExamParticipant')
            && $authUser->can(ExamPermission::ViewExam->value);
    }

    public function view(AuthUser $authUser, ExamParticipant $examParticipant): bool
    {
        $exam = $examParticipant->examDefinition;

        return $authUser->can('View:ExamParticipant')
            && $exam !== null
            && $this->canViewExam($authUser, $exam);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExamParticipant')
            && $authUser->can(ExamPermission::UpdateExam->value);
    }

    public function update(AuthUser $authUser, ExamParticipant $examParticipant): bool
    {
        $exam = $examParticipant->examDefinition;

        return $authUser->can('Update:ExamParticipant')
            && $exam !== null
            && $this->canAccessExam($authUser, $exam, ExamPermission::UpdateExam->value);
    }

    public function delete(AuthUser $authUser, ExamParticipant $examParticipant): bool
    {
        $exam = $examParticipant->examDefinition;

        return $authUser->can('Delete:ExamParticipant')
            && $exam !== null
            && $this->canAccessExam($authUser, $exam, ExamPermission::UpdateExam->value);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExamParticipant')
            && $authUser->can(ExamPermission::UpdateExam->value);
    }

    public function restore(AuthUser $authUser, ExamParticipant $examParticipant): bool
    {
        return $this->update($authUser, $examParticipant);
    }

    public function forceDelete(AuthUser $authUser, ExamParticipant $examParticipant): bool
    {
        return $this->delete($authUser, $examParticipant);
    }

    public function regenerateToken(AuthUser $authUser, ExamParticipant $examParticipant): bool
    {
        $exam = $examParticipant->examDefinition;

        return $exam !== null
            && $this->canAccessExam($authUser, $exam, ExamPermission::ManageExamToken->value);
    }
}
