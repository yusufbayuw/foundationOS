<?php

declare(strict_types=1);

namespace Modules\Property\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Property\Models\LeaseDeposit;

class LeaseDepositPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LeaseDeposit');
    }

    public function view(AuthUser $authUser, LeaseDeposit $leaseDeposit): bool
    {
        return $authUser->can('View:LeaseDeposit');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LeaseDeposit');
    }

    public function update(AuthUser $authUser, LeaseDeposit $leaseDeposit): bool
    {
        return $authUser->can('Update:LeaseDeposit');
    }

    public function delete(AuthUser $authUser, LeaseDeposit $leaseDeposit): bool
    {
        return $authUser->can('Delete:LeaseDeposit');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LeaseDeposit');
    }

    public function restore(AuthUser $authUser, LeaseDeposit $leaseDeposit): bool
    {
        return $authUser->can('Restore:LeaseDeposit');
    }

    public function forceDelete(AuthUser $authUser, LeaseDeposit $leaseDeposit): bool
    {
        return $authUser->can('ForceDelete:LeaseDeposit');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LeaseDeposit');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LeaseDeposit');
    }

    public function replicate(AuthUser $authUser, LeaseDeposit $leaseDeposit): bool
    {
        return $authUser->can('Replicate:LeaseDeposit');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LeaseDeposit');
    }
}
