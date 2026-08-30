<?php

declare(strict_types=1);

namespace Modules\Facility\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Facility\Models\FacilityRental;

class FacilityRentalPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FacilityRental');
    }

    public function view(AuthUser $authUser, FacilityRental $facilityRental): bool
    {
        return $authUser->can('View:FacilityRental');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FacilityRental');
    }

    public function update(AuthUser $authUser, FacilityRental $facilityRental): bool
    {
        return $authUser->can('Update:FacilityRental');
    }

    public function delete(AuthUser $authUser, FacilityRental $facilityRental): bool
    {
        return $authUser->can('Delete:FacilityRental');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FacilityRental');
    }

    public function restore(AuthUser $authUser, FacilityRental $facilityRental): bool
    {
        return $authUser->can('Restore:FacilityRental');
    }

    public function forceDelete(AuthUser $authUser, FacilityRental $facilityRental): bool
    {
        return $authUser->can('ForceDelete:FacilityRental');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FacilityRental');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FacilityRental');
    }

    public function replicate(AuthUser $authUser, FacilityRental $facilityRental): bool
    {
        return $authUser->can('Replicate:FacilityRental');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FacilityRental');
    }
}
