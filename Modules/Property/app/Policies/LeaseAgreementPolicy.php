<?php

declare(strict_types=1);

namespace Modules\Property\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Property\Models\LeaseAgreement;

class LeaseAgreementPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LeaseAgreement');
    }

    public function view(AuthUser $authUser, LeaseAgreement $leaseAgreement): bool
    {
        return $authUser->can('View:LeaseAgreement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LeaseAgreement');
    }

    public function update(AuthUser $authUser, LeaseAgreement $leaseAgreement): bool
    {
        return $authUser->can('Update:LeaseAgreement');
    }

    public function delete(AuthUser $authUser, LeaseAgreement $leaseAgreement): bool
    {
        return $authUser->can('Delete:LeaseAgreement');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LeaseAgreement');
    }

    public function restore(AuthUser $authUser, LeaseAgreement $leaseAgreement): bool
    {
        return $authUser->can('Restore:LeaseAgreement');
    }

    public function forceDelete(AuthUser $authUser, LeaseAgreement $leaseAgreement): bool
    {
        return $authUser->can('ForceDelete:LeaseAgreement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LeaseAgreement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LeaseAgreement');
    }

    public function replicate(AuthUser $authUser, LeaseAgreement $leaseAgreement): bool
    {
        return $authUser->can('Replicate:LeaseAgreement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LeaseAgreement');
    }
}
