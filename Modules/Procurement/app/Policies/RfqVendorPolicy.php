<?php

declare(strict_types=1);

namespace Modules\Procurement\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Procurement\Models\RfqVendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class RfqVendorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RfqVendor');
    }

    public function view(AuthUser $authUser, RfqVendor $rfqVendor): bool
    {
        return $authUser->can('View:RfqVendor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RfqVendor');
    }

    public function update(AuthUser $authUser, RfqVendor $rfqVendor): bool
    {
        return $authUser->can('Update:RfqVendor');
    }

    public function delete(AuthUser $authUser, RfqVendor $rfqVendor): bool
    {
        return $authUser->can('Delete:RfqVendor');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RfqVendor');
    }

    public function restore(AuthUser $authUser, RfqVendor $rfqVendor): bool
    {
        return $authUser->can('Restore:RfqVendor');
    }

    public function forceDelete(AuthUser $authUser, RfqVendor $rfqVendor): bool
    {
        return $authUser->can('ForceDelete:RfqVendor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RfqVendor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RfqVendor');
    }

    public function replicate(AuthUser $authUser, RfqVendor $rfqVendor): bool
    {
        return $authUser->can('Replicate:RfqVendor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RfqVendor');
    }

}