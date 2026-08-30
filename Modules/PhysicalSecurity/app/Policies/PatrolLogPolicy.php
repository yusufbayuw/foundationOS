<?php

declare(strict_types=1);

namespace Modules\PhysicalSecurity\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PhysicalSecurity\Models\PatrolLog;

class PatrolLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PatrolLog');
    }

    public function view(AuthUser $authUser, PatrolLog $patrolLog): bool
    {
        return $authUser->can('View:PatrolLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PatrolLog');
    }

    public function update(AuthUser $authUser, PatrolLog $patrolLog): bool
    {
        return $authUser->can('Update:PatrolLog');
    }

    public function delete(AuthUser $authUser, PatrolLog $patrolLog): bool
    {
        return $authUser->can('Delete:PatrolLog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PatrolLog');
    }

    public function restore(AuthUser $authUser, PatrolLog $patrolLog): bool
    {
        return $authUser->can('Restore:PatrolLog');
    }

    public function forceDelete(AuthUser $authUser, PatrolLog $patrolLog): bool
    {
        return $authUser->can('ForceDelete:PatrolLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PatrolLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PatrolLog');
    }

    public function replicate(AuthUser $authUser, PatrolLog $patrolLog): bool
    {
        return $authUser->can('Replicate:PatrolLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PatrolLog');
    }
}
