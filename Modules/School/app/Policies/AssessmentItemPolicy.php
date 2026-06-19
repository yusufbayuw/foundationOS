<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\School\Models\AssessmentItem;

class AssessmentItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AssessmentItem');
    }

    public function view(AuthUser $authUser, AssessmentItem $assessmentItem): bool
    {
        return $authUser->can('View:AssessmentItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AssessmentItem');
    }

    public function update(AuthUser $authUser, AssessmentItem $assessmentItem): bool
    {
        return $authUser->can('Update:AssessmentItem');
    }

    public function delete(AuthUser $authUser, AssessmentItem $assessmentItem): bool
    {
        return $authUser->can('Delete:AssessmentItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AssessmentItem');
    }

    public function restore(AuthUser $authUser, AssessmentItem $assessmentItem): bool
    {
        return $authUser->can('Restore:AssessmentItem');
    }

    public function forceDelete(AuthUser $authUser, AssessmentItem $assessmentItem): bool
    {
        return $authUser->can('ForceDelete:AssessmentItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AssessmentItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AssessmentItem');
    }

    public function replicate(AuthUser $authUser, AssessmentItem $assessmentItem): bool
    {
        return $authUser->can('Replicate:AssessmentItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AssessmentItem');
    }
}
