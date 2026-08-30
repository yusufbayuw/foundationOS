<?php

declare(strict_types=1);

namespace Modules\Capacity\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Capacity\Models\CapacityUtilization;

class CapacityUtilizationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CapacityUtilization');
    }

    public function view(AuthUser $authUser, CapacityUtilization $capacityUtilization): bool
    {
        return $authUser->can('View:CapacityUtilization');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CapacityUtilization');
    }

    public function update(AuthUser $authUser, CapacityUtilization $capacityUtilization): bool
    {
        return $authUser->can('Update:CapacityUtilization');
    }

    public function delete(AuthUser $authUser, CapacityUtilization $capacityUtilization): bool
    {
        return $authUser->can('Delete:CapacityUtilization');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CapacityUtilization');
    }

    public function restore(AuthUser $authUser, CapacityUtilization $capacityUtilization): bool
    {
        return $authUser->can('Restore:CapacityUtilization');
    }

    public function forceDelete(AuthUser $authUser, CapacityUtilization $capacityUtilization): bool
    {
        return $authUser->can('ForceDelete:CapacityUtilization');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CapacityUtilization');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CapacityUtilization');
    }

    public function replicate(AuthUser $authUser, CapacityUtilization $capacityUtilization): bool
    {
        return $authUser->can('Replicate:CapacityUtilization');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CapacityUtilization');
    }
}
