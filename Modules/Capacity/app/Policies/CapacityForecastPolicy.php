<?php

declare(strict_types=1);

namespace Modules\Capacity\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Capacity\Models\CapacityForecast;

class CapacityForecastPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CapacityForecast');
    }

    public function view(AuthUser $authUser, CapacityForecast $capacityForecast): bool
    {
        return $authUser->can('View:CapacityForecast');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CapacityForecast');
    }

    public function update(AuthUser $authUser, CapacityForecast $capacityForecast): bool
    {
        return $authUser->can('Update:CapacityForecast');
    }

    public function delete(AuthUser $authUser, CapacityForecast $capacityForecast): bool
    {
        return $authUser->can('Delete:CapacityForecast');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CapacityForecast');
    }

    public function restore(AuthUser $authUser, CapacityForecast $capacityForecast): bool
    {
        return $authUser->can('Restore:CapacityForecast');
    }

    public function forceDelete(AuthUser $authUser, CapacityForecast $capacityForecast): bool
    {
        return $authUser->can('ForceDelete:CapacityForecast');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CapacityForecast');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CapacityForecast');
    }

    public function replicate(AuthUser $authUser, CapacityForecast $capacityForecast): bool
    {
        return $authUser->can('Replicate:CapacityForecast');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CapacityForecast');
    }
}
