<?php

declare(strict_types=1);

namespace Modules\Procurement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Policies\Concerns\AuthorizesPrint;
use Modules\Procurement\Models\PurchaseRequisition;

class PurchaseRequisitionPolicy
{
    use AuthorizesPrint, HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PurchaseRequisition');
    }

    public function view(AuthUser $authUser, PurchaseRequisition $purchaseRequisition): bool
    {
        return $authUser->can('View:PurchaseRequisition');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PurchaseRequisition');
    }

    public function update(AuthUser $authUser, PurchaseRequisition $purchaseRequisition): bool
    {
        return $authUser->can('Update:PurchaseRequisition');
    }

    public function delete(AuthUser $authUser, PurchaseRequisition $purchaseRequisition): bool
    {
        return $authUser->can('Delete:PurchaseRequisition');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PurchaseRequisition');
    }

    public function restore(AuthUser $authUser, PurchaseRequisition $purchaseRequisition): bool
    {
        return $authUser->can('Restore:PurchaseRequisition');
    }

    public function forceDelete(AuthUser $authUser, PurchaseRequisition $purchaseRequisition): bool
    {
        return $authUser->can('ForceDelete:PurchaseRequisition');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PurchaseRequisition');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PurchaseRequisition');
    }

    public function replicate(AuthUser $authUser, PurchaseRequisition $purchaseRequisition): bool
    {
        return $authUser->can('Replicate:PurchaseRequisition');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PurchaseRequisition');
    }
}
