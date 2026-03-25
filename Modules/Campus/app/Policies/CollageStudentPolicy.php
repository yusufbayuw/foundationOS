<?php

declare(strict_types=1);

namespace Modules\Campus\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Campus\Models\CollageStudent;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollageStudentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CollageStudent');
    }

    public function view(AuthUser $authUser, CollageStudent $collageStudent): bool
    {
        return $authUser->can('View:CollageStudent');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CollageStudent');
    }

    public function update(AuthUser $authUser, CollageStudent $collageStudent): bool
    {
        return $authUser->can('Update:CollageStudent');
    }

    public function delete(AuthUser $authUser, CollageStudent $collageStudent): bool
    {
        return $authUser->can('Delete:CollageStudent');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CollageStudent');
    }

    public function restore(AuthUser $authUser, CollageStudent $collageStudent): bool
    {
        return $authUser->can('Restore:CollageStudent');
    }

    public function forceDelete(AuthUser $authUser, CollageStudent $collageStudent): bool
    {
        return $authUser->can('ForceDelete:CollageStudent');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CollageStudent');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CollageStudent');
    }

    public function replicate(AuthUser $authUser, CollageStudent $collageStudent): bool
    {
        return $authUser->can('Replicate:CollageStudent');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CollageStudent');
    }

}