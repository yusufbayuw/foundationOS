<?php

declare(strict_types=1);

namespace Modules\Alumni\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Alumni\Models\AlumnusEmployment;

class AlumnusEmploymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AlumnusEmployment');
    }

    public function view(AuthUser $authUser, AlumnusEmployment $alumnusEmployment): bool
    {
        return $authUser->can('View:AlumnusEmployment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AlumnusEmployment');
    }

    public function update(AuthUser $authUser, AlumnusEmployment $alumnusEmployment): bool
    {
        return $authUser->can('Update:AlumnusEmployment');
    }

    public function delete(AuthUser $authUser, AlumnusEmployment $alumnusEmployment): bool
    {
        return $authUser->can('Delete:AlumnusEmployment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AlumnusEmployment');
    }

    public function restore(AuthUser $authUser, AlumnusEmployment $alumnusEmployment): bool
    {
        return $authUser->can('Restore:AlumnusEmployment');
    }

    public function forceDelete(AuthUser $authUser, AlumnusEmployment $alumnusEmployment): bool
    {
        return $authUser->can('ForceDelete:AlumnusEmployment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AlumnusEmployment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AlumnusEmployment');
    }

    public function replicate(AuthUser $authUser, AlumnusEmployment $alumnusEmployment): bool
    {
        return $authUser->can('Replicate:AlumnusEmployment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AlumnusEmployment');
    }
}
