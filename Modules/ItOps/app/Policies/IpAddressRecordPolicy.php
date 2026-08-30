<?php

declare(strict_types=1);

namespace Modules\ItOps\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ItOps\Models\IpAddressRecord;

class IpAddressRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:IpAddressRecord');
    }

    public function view(AuthUser $authUser, IpAddressRecord $ipAddressRecord): bool
    {
        return $authUser->can('View:IpAddressRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:IpAddressRecord');
    }

    public function update(AuthUser $authUser, IpAddressRecord $ipAddressRecord): bool
    {
        return $authUser->can('Update:IpAddressRecord');
    }

    public function delete(AuthUser $authUser, IpAddressRecord $ipAddressRecord): bool
    {
        return $authUser->can('Delete:IpAddressRecord');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:IpAddressRecord');
    }

    public function restore(AuthUser $authUser, IpAddressRecord $ipAddressRecord): bool
    {
        return $authUser->can('Restore:IpAddressRecord');
    }

    public function forceDelete(AuthUser $authUser, IpAddressRecord $ipAddressRecord): bool
    {
        return $authUser->can('ForceDelete:IpAddressRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:IpAddressRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:IpAddressRecord');
    }

    public function replicate(AuthUser $authUser, IpAddressRecord $ipAddressRecord): bool
    {
        return $authUser->can('Replicate:IpAddressRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:IpAddressRecord');
    }
}
