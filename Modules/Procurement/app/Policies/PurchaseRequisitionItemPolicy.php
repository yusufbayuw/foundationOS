<?php

declare(strict_types=1);

namespace Modules\Procurement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Procurement\Models\PurchaseRequisitionItem;

class PurchaseRequisitionItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PurchaseRequisitionItem');
    }

    public function view(AuthUser $authUser, PurchaseRequisitionItem $purchaseRequisitionItem): bool
    {
        return $authUser->can('View:PurchaseRequisitionItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PurchaseRequisitionItem');
    }

    public function update(AuthUser $authUser, PurchaseRequisitionItem $purchaseRequisitionItem): bool
    {
        return $authUser->can('Update:PurchaseRequisitionItem');
    }

    public function delete(AuthUser $authUser, PurchaseRequisitionItem $purchaseRequisitionItem): bool
    {
        return $authUser->can('Delete:PurchaseRequisitionItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PurchaseRequisitionItem');
    }

    public function restore(AuthUser $authUser, PurchaseRequisitionItem $purchaseRequisitionItem): bool
    {
        return $authUser->can('Restore:PurchaseRequisitionItem');
    }

    public function forceDelete(AuthUser $authUser, PurchaseRequisitionItem $purchaseRequisitionItem): bool
    {
        return $authUser->can('ForceDelete:PurchaseRequisitionItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PurchaseRequisitionItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PurchaseRequisitionItem');
    }

    public function replicate(AuthUser $authUser, PurchaseRequisitionItem $purchaseRequisitionItem): bool
    {
        return $authUser->can('Replicate:PurchaseRequisitionItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PurchaseRequisitionItem');
    }
}
