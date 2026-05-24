<?php

declare(strict_types=1);

namespace Modules\Capacity\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Capacity\Models\CapacityResource;

class CapacityResourcePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CapacityResource');
    }

    public function view(AuthUser $authUser, CapacityResource $capacityResource): bool
    {
        return $authUser->can('View:CapacityResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CapacityResource');
    }

    public function update(AuthUser $authUser, CapacityResource $capacityResource): bool
    {
        return $authUser->can('Update:CapacityResource');
    }

    public function delete(AuthUser $authUser, CapacityResource $capacityResource): bool
    {
        return $authUser->can('Delete:CapacityResource');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CapacityResource');
    }

    public function restore(AuthUser $authUser, CapacityResource $capacityResource): bool
    {
        return $authUser->can('Restore:CapacityResource');
    }

    public function forceDelete(AuthUser $authUser, CapacityResource $capacityResource): bool
    {
        return $authUser->can('ForceDelete:CapacityResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CapacityResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CapacityResource');
    }

    public function replicate(AuthUser $authUser, CapacityResource $capacityResource): bool
    {
        return $authUser->can('Replicate:CapacityResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CapacityResource');
    }
}
