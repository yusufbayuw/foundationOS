<?php

declare(strict_types=1);

namespace Modules\Property\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Property\Models\PropertyMaintenance;

class PropertyMaintenancePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PropertyMaintenance');
    }

    public function view(AuthUser $authUser, PropertyMaintenance $propertyMaintenance): bool
    {
        return $authUser->can('View:PropertyMaintenance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PropertyMaintenance');
    }

    public function update(AuthUser $authUser, PropertyMaintenance $propertyMaintenance): bool
    {
        return $authUser->can('Update:PropertyMaintenance');
    }

    public function delete(AuthUser $authUser, PropertyMaintenance $propertyMaintenance): bool
    {
        return $authUser->can('Delete:PropertyMaintenance');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PropertyMaintenance');
    }

    public function restore(AuthUser $authUser, PropertyMaintenance $propertyMaintenance): bool
    {
        return $authUser->can('Restore:PropertyMaintenance');
    }

    public function forceDelete(AuthUser $authUser, PropertyMaintenance $propertyMaintenance): bool
    {
        return $authUser->can('ForceDelete:PropertyMaintenance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PropertyMaintenance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PropertyMaintenance');
    }

    public function replicate(AuthUser $authUser, PropertyMaintenance $propertyMaintenance): bool
    {
        return $authUser->can('Replicate:PropertyMaintenance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PropertyMaintenance');
    }
}
