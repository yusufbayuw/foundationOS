<?php

declare(strict_types=1);

namespace Modules\Procurement\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Procurement\Models\VendorBill;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorBillPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VendorBill');
    }

    public function view(AuthUser $authUser, VendorBill $vendorBill): bool
    {
        return $authUser->can('View:VendorBill');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VendorBill');
    }

    public function update(AuthUser $authUser, VendorBill $vendorBill): bool
    {
        return $authUser->can('Update:VendorBill');
    }

    public function delete(AuthUser $authUser, VendorBill $vendorBill): bool
    {
        return $authUser->can('Delete:VendorBill');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:VendorBill');
    }

    public function restore(AuthUser $authUser, VendorBill $vendorBill): bool
    {
        return $authUser->can('Restore:VendorBill');
    }

    public function forceDelete(AuthUser $authUser, VendorBill $vendorBill): bool
    {
        return $authUser->can('ForceDelete:VendorBill');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VendorBill');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VendorBill');
    }

    public function replicate(AuthUser $authUser, VendorBill $vendorBill): bool
    {
        return $authUser->can('Replicate:VendorBill');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VendorBill');
    }

}