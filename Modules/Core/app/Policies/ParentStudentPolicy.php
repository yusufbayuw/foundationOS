<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\ParentStudent;

class ParentStudentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ParentStudent');
    }

    public function view(AuthUser $authUser, ParentStudent $parentStudent): bool
    {
        return $authUser->can('View:ParentStudent');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ParentStudent');
    }

    public function update(AuthUser $authUser, ParentStudent $parentStudent): bool
    {
        return $authUser->can('Update:ParentStudent');
    }

    public function delete(AuthUser $authUser, ParentStudent $parentStudent): bool
    {
        return $authUser->can('Delete:ParentStudent');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ParentStudent');
    }

    public function restore(AuthUser $authUser, ParentStudent $parentStudent): bool
    {
        return $authUser->can('Restore:ParentStudent');
    }

    public function forceDelete(AuthUser $authUser, ParentStudent $parentStudent): bool
    {
        return $authUser->can('ForceDelete:ParentStudent');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ParentStudent');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ParentStudent');
    }

    public function replicate(AuthUser $authUser, ParentStudent $parentStudent): bool
    {
        return $authUser->can('Replicate:ParentStudent');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ParentStudent');
    }
}
