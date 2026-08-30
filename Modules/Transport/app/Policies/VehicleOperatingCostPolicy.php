<?php

declare(strict_types=1);

namespace Modules\Transport\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Transport\Models\VehicleOperatingCost;

class VehicleOperatingCostPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VehicleOperatingCost');
    }

    public function view(AuthUser $authUser, VehicleOperatingCost $vehicleOperatingCost): bool
    {
        return $authUser->can('View:VehicleOperatingCost');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VehicleOperatingCost');
    }

    public function update(AuthUser $authUser, VehicleOperatingCost $vehicleOperatingCost): bool
    {
        return $authUser->can('Update:VehicleOperatingCost');
    }

    public function delete(AuthUser $authUser, VehicleOperatingCost $vehicleOperatingCost): bool
    {
        return $authUser->can('Delete:VehicleOperatingCost');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:VehicleOperatingCost');
    }

    public function restore(AuthUser $authUser, VehicleOperatingCost $vehicleOperatingCost): bool
    {
        return $authUser->can('Restore:VehicleOperatingCost');
    }

    public function forceDelete(AuthUser $authUser, VehicleOperatingCost $vehicleOperatingCost): bool
    {
        return $authUser->can('ForceDelete:VehicleOperatingCost');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VehicleOperatingCost');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VehicleOperatingCost');
    }

    public function replicate(AuthUser $authUser, VehicleOperatingCost $vehicleOperatingCost): bool
    {
        return $authUser->can('Replicate:VehicleOperatingCost');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VehicleOperatingCost');
    }
}
