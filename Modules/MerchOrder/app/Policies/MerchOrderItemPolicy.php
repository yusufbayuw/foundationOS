<?php

declare(strict_types=1);

namespace Modules\MerchOrder\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\MerchOrder\Models\MerchOrderItem;

class MerchOrderItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MerchOrderItem');
    }

    public function view(AuthUser $authUser, MerchOrderItem $merchOrderItem): bool
    {
        return $authUser->can('View:MerchOrderItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MerchOrderItem');
    }

    public function update(AuthUser $authUser, MerchOrderItem $merchOrderItem): bool
    {
        return $authUser->can('Update:MerchOrderItem');
    }

    public function delete(AuthUser $authUser, MerchOrderItem $merchOrderItem): bool
    {
        return $authUser->can('Delete:MerchOrderItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MerchOrderItem');
    }

    public function restore(AuthUser $authUser, MerchOrderItem $merchOrderItem): bool
    {
        return $authUser->can('Restore:MerchOrderItem');
    }

    public function forceDelete(AuthUser $authUser, MerchOrderItem $merchOrderItem): bool
    {
        return $authUser->can('ForceDelete:MerchOrderItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MerchOrderItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MerchOrderItem');
    }

    public function replicate(AuthUser $authUser, MerchOrderItem $merchOrderItem): bool
    {
        return $authUser->can('Replicate:MerchOrderItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MerchOrderItem');
    }
}
