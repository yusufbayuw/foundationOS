<?php

declare(strict_types=1);

namespace Modules\Procurement\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Procurement\Models\VendorBillItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorBillItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VendorBillItem');
    }

    public function view(AuthUser $authUser, VendorBillItem $vendorBillItem): bool
    {
        return $authUser->can('View:VendorBillItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VendorBillItem');
    }

    public function update(AuthUser $authUser, VendorBillItem $vendorBillItem): bool
    {
        return $authUser->can('Update:VendorBillItem');
    }

    public function delete(AuthUser $authUser, VendorBillItem $vendorBillItem): bool
    {
        return $authUser->can('Delete:VendorBillItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:VendorBillItem');
    }

    public function restore(AuthUser $authUser, VendorBillItem $vendorBillItem): bool
    {
        return $authUser->can('Restore:VendorBillItem');
    }

    public function forceDelete(AuthUser $authUser, VendorBillItem $vendorBillItem): bool
    {
        return $authUser->can('ForceDelete:VendorBillItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VendorBillItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VendorBillItem');
    }

    public function replicate(AuthUser $authUser, VendorBillItem $vendorBillItem): bool
    {
        return $authUser->can('Replicate:VendorBillItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VendorBillItem');
    }

}