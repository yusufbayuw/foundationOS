<?php

declare(strict_types=1);

namespace Modules\Campus\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Campus\Models\StudyResult;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudyResultPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudyResult');
    }

    public function view(AuthUser $authUser, StudyResult $studyResult): bool
    {
        return $authUser->can('View:StudyResult');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudyResult');
    }

    public function update(AuthUser $authUser, StudyResult $studyResult): bool
    {
        return $authUser->can('Update:StudyResult');
    }

    public function delete(AuthUser $authUser, StudyResult $studyResult): bool
    {
        return $authUser->can('Delete:StudyResult');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudyResult');
    }

    public function restore(AuthUser $authUser, StudyResult $studyResult): bool
    {
        return $authUser->can('Restore:StudyResult');
    }

    public function forceDelete(AuthUser $authUser, StudyResult $studyResult): bool
    {
        return $authUser->can('ForceDelete:StudyResult');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudyResult');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudyResult');
    }

    public function replicate(AuthUser $authUser, StudyResult $studyResult): bool
    {
        return $authUser->can('Replicate:StudyResult');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudyResult');
    }

}