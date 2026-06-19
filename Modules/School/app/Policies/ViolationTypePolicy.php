<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\School\Models\ViolationType;

class ViolationTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ViolationType');
    }

    public function view(AuthUser $authUser, ViolationType $violationType): bool
    {
        return $authUser->can('View:ViolationType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ViolationType');
    }

    public function update(AuthUser $authUser, ViolationType $violationType): bool
    {
        return $authUser->can('Update:ViolationType');
    }

    public function delete(AuthUser $authUser, ViolationType $violationType): bool
    {
        return $authUser->can('Delete:ViolationType');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ViolationType');
    }

    public function restore(AuthUser $authUser, ViolationType $violationType): bool
    {
        return $authUser->can('Restore:ViolationType');
    }

    public function forceDelete(AuthUser $authUser, ViolationType $violationType): bool
    {
        return $authUser->can('ForceDelete:ViolationType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ViolationType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ViolationType');
    }

    public function replicate(AuthUser $authUser, ViolationType $violationType): bool
    {
        return $authUser->can('Replicate:ViolationType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ViolationType');
    }
}
