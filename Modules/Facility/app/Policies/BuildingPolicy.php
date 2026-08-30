<?php

declare(strict_types=1);

namespace Modules\Facility\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Facility\Models\Building;

class BuildingPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Building');
    }

    public function view(AuthUser $authUser, Building $building): bool
    {
        return $authUser->can('View:Building');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Building');
    }

    public function update(AuthUser $authUser, Building $building): bool
    {
        return $authUser->can('Update:Building');
    }

    public function delete(AuthUser $authUser, Building $building): bool
    {
        return $authUser->can('Delete:Building');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Building');
    }

    public function restore(AuthUser $authUser, Building $building): bool
    {
        return $authUser->can('Restore:Building');
    }

    public function forceDelete(AuthUser $authUser, Building $building): bool
    {
        return $authUser->can('ForceDelete:Building');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Building');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Building');
    }

    public function replicate(AuthUser $authUser, Building $building): bool
    {
        return $authUser->can('Replicate:Building');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Building');
    }
}
