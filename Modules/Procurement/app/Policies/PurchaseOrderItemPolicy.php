<?php

declare(strict_types=1);

namespace Modules\Procurement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Procurement\Models\PurchaseOrderItem;

class PurchaseOrderItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PurchaseOrderItem');
    }

    public function view(AuthUser $authUser, PurchaseOrderItem $purchaseOrderItem): bool
    {
        return $authUser->can('View:PurchaseOrderItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PurchaseOrderItem');
    }

    public function update(AuthUser $authUser, PurchaseOrderItem $purchaseOrderItem): bool
    {
        return $authUser->can('Update:PurchaseOrderItem');
    }

    public function delete(AuthUser $authUser, PurchaseOrderItem $purchaseOrderItem): bool
    {
        return $authUser->can('Delete:PurchaseOrderItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PurchaseOrderItem');
    }

    public function restore(AuthUser $authUser, PurchaseOrderItem $purchaseOrderItem): bool
    {
        return $authUser->can('Restore:PurchaseOrderItem');
    }

    public function forceDelete(AuthUser $authUser, PurchaseOrderItem $purchaseOrderItem): bool
    {
        return $authUser->can('ForceDelete:PurchaseOrderItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PurchaseOrderItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PurchaseOrderItem');
    }

    public function replicate(AuthUser $authUser, PurchaseOrderItem $purchaseOrderItem): bool
    {
        return $authUser->can('Replicate:PurchaseOrderItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PurchaseOrderItem');
    }
}
