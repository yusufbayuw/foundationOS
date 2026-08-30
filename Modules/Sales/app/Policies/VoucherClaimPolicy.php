<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Sales\Models\VoucherClaim;

class VoucherClaimPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VoucherClaim');
    }

    public function view(AuthUser $authUser, VoucherClaim $voucherClaim): bool
    {
        return $authUser->can('View:VoucherClaim');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VoucherClaim');
    }

    public function update(AuthUser $authUser, VoucherClaim $voucherClaim): bool
    {
        return $authUser->can('Update:VoucherClaim');
    }

    public function delete(AuthUser $authUser, VoucherClaim $voucherClaim): bool
    {
        return $authUser->can('Delete:VoucherClaim');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:VoucherClaim');
    }

    public function restore(AuthUser $authUser, VoucherClaim $voucherClaim): bool
    {
        return $authUser->can('Restore:VoucherClaim');
    }

    public function forceDelete(AuthUser $authUser, VoucherClaim $voucherClaim): bool
    {
        return $authUser->can('ForceDelete:VoucherClaim');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VoucherClaim');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VoucherClaim');
    }

    public function replicate(AuthUser $authUser, VoucherClaim $voucherClaim): bool
    {
        return $authUser->can('Replicate:VoucherClaim');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VoucherClaim');
    }
}
