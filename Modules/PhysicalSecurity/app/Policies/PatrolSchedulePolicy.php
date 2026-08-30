<?php

declare(strict_types=1);

namespace Modules\PhysicalSecurity\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PhysicalSecurity\Models\PatrolSchedule;

class PatrolSchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PatrolSchedule');
    }

    public function view(AuthUser $authUser, PatrolSchedule $patrolSchedule): bool
    {
        return $authUser->can('View:PatrolSchedule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PatrolSchedule');
    }

    public function update(AuthUser $authUser, PatrolSchedule $patrolSchedule): bool
    {
        return $authUser->can('Update:PatrolSchedule');
    }

    public function delete(AuthUser $authUser, PatrolSchedule $patrolSchedule): bool
    {
        return $authUser->can('Delete:PatrolSchedule');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PatrolSchedule');
    }

    public function restore(AuthUser $authUser, PatrolSchedule $patrolSchedule): bool
    {
        return $authUser->can('Restore:PatrolSchedule');
    }

    public function forceDelete(AuthUser $authUser, PatrolSchedule $patrolSchedule): bool
    {
        return $authUser->can('ForceDelete:PatrolSchedule');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PatrolSchedule');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PatrolSchedule');
    }

    public function replicate(AuthUser $authUser, PatrolSchedule $patrolSchedule): bool
    {
        return $authUser->can('Replicate:PatrolSchedule');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PatrolSchedule');
    }
}
