<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\School\Models\StudentAssessmentAnswer;

class StudentAssessmentAnswerPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentAssessmentAnswer');
    }

    public function view(AuthUser $authUser, StudentAssessmentAnswer $studentAssessmentAnswer): bool
    {
        return $authUser->can('View:StudentAssessmentAnswer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentAssessmentAnswer');
    }

    public function update(AuthUser $authUser, StudentAssessmentAnswer $studentAssessmentAnswer): bool
    {
        return $authUser->can('Update:StudentAssessmentAnswer');
    }

    public function delete(AuthUser $authUser, StudentAssessmentAnswer $studentAssessmentAnswer): bool
    {
        return $authUser->can('Delete:StudentAssessmentAnswer');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudentAssessmentAnswer');
    }

    public function restore(AuthUser $authUser, StudentAssessmentAnswer $studentAssessmentAnswer): bool
    {
        return $authUser->can('Restore:StudentAssessmentAnswer');
    }

    public function forceDelete(AuthUser $authUser, StudentAssessmentAnswer $studentAssessmentAnswer): bool
    {
        return $authUser->can('ForceDelete:StudentAssessmentAnswer');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentAssessmentAnswer');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentAssessmentAnswer');
    }

    public function replicate(AuthUser $authUser, StudentAssessmentAnswer $studentAssessmentAnswer): bool
    {
        return $authUser->can('Replicate:StudentAssessmentAnswer');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentAssessmentAnswer');
    }
}
