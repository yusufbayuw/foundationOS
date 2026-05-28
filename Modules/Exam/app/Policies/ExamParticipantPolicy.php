<?php

declare(strict_types=1);

namespace Modules\Exam\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Exam\Models\ExamParticipant;

class ExamParticipantPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExamParticipant');
    }

    public function view(AuthUser $authUser, ExamParticipant $examParticipant): bool
    {
        return $authUser->can('View:ExamParticipant');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExamParticipant');
    }

    public function update(AuthUser $authUser, ExamParticipant $examParticipant): bool
    {
        return $authUser->can('Update:ExamParticipant');
    }

    public function delete(AuthUser $authUser, ExamParticipant $examParticipant): bool
    {
        return $authUser->can('Delete:ExamParticipant');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExamParticipant');
    }

    public function restore(AuthUser $authUser, ExamParticipant $examParticipant): bool
    {
        return $authUser->can('Restore:ExamParticipant');
    }

    public function forceDelete(AuthUser $authUser, ExamParticipant $examParticipant): bool
    {
        return $authUser->can('ForceDelete:ExamParticipant');
    }
}
