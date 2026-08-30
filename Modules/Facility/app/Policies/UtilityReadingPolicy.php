<?php

declare(strict_types=1);

namespace Modules\Facility\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Facility\Models\UtilityReading;

class UtilityReadingPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UtilityReading');
    }

    public function view(AuthUser $authUser, UtilityReading $utilityReading): bool
    {
        return $authUser->can('View:UtilityReading');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UtilityReading');
    }

    public function update(AuthUser $authUser, UtilityReading $utilityReading): bool
    {
        return $authUser->can('Update:UtilityReading');
    }

    public function delete(AuthUser $authUser, UtilityReading $utilityReading): bool
    {
        return $authUser->can('Delete:UtilityReading');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:UtilityReading');
    }

    public function restore(AuthUser $authUser, UtilityReading $utilityReading): bool
    {
        return $authUser->can('Restore:UtilityReading');
    }

    public function forceDelete(AuthUser $authUser, UtilityReading $utilityReading): bool
    {
        return $authUser->can('ForceDelete:UtilityReading');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UtilityReading');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UtilityReading');
    }

    public function replicate(AuthUser $authUser, UtilityReading $utilityReading): bool
    {
        return $authUser->can('Replicate:UtilityReading');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UtilityReading');
    }
}
