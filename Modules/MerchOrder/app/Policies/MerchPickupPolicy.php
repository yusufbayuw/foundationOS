<?php

declare(strict_types=1);

namespace Modules\MerchOrder\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\MerchOrder\Models\MerchPickup;

class MerchPickupPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MerchPickup');
    }

    public function view(AuthUser $authUser, MerchPickup $merchPickup): bool
    {
        return $authUser->can('View:MerchPickup');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MerchPickup');
    }

    public function update(AuthUser $authUser, MerchPickup $merchPickup): bool
    {
        return $authUser->can('Update:MerchPickup');
    }

    public function delete(AuthUser $authUser, MerchPickup $merchPickup): bool
    {
        return $authUser->can('Delete:MerchPickup');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MerchPickup');
    }

    public function restore(AuthUser $authUser, MerchPickup $merchPickup): bool
    {
        return $authUser->can('Restore:MerchPickup');
    }

    public function forceDelete(AuthUser $authUser, MerchPickup $merchPickup): bool
    {
        return $authUser->can('ForceDelete:MerchPickup');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MerchPickup');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MerchPickup');
    }

    public function replicate(AuthUser $authUser, MerchPickup $merchPickup): bool
    {
        return $authUser->can('Replicate:MerchPickup');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MerchPickup');
    }
}
