<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\School\Models\ClassStudent;

class ClassStudentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ClassStudent');
    }

    public function view(AuthUser $authUser, ClassStudent $classStudent): bool
    {
        return $authUser->can('View:ClassStudent');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ClassStudent');
    }

    public function update(AuthUser $authUser, ClassStudent $classStudent): bool
    {
        return $authUser->can('Update:ClassStudent');
    }

    public function delete(AuthUser $authUser, ClassStudent $classStudent): bool
    {
        return $authUser->can('Delete:ClassStudent');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ClassStudent');
    }

    public function restore(AuthUser $authUser, ClassStudent $classStudent): bool
    {
        return $authUser->can('Restore:ClassStudent');
    }

    public function forceDelete(AuthUser $authUser, ClassStudent $classStudent): bool
    {
        return $authUser->can('ForceDelete:ClassStudent');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ClassStudent');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ClassStudent');
    }

    public function replicate(AuthUser $authUser, ClassStudent $classStudent): bool
    {
        return $authUser->can('Replicate:ClassStudent');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ClassStudent');
    }
}
