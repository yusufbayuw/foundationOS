<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Sales\Models\SalesOrderItem;

class SalesOrderItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SalesOrderItem');
    }

    public function view(AuthUser $authUser, SalesOrderItem $salesOrderItem): bool
    {
        return $authUser->can('View:SalesOrderItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SalesOrderItem');
    }

    public function update(AuthUser $authUser, SalesOrderItem $salesOrderItem): bool
    {
        return $authUser->can('Update:SalesOrderItem');
    }

    public function delete(AuthUser $authUser, SalesOrderItem $salesOrderItem): bool
    {
        return $authUser->can('Delete:SalesOrderItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SalesOrderItem');
    }

    public function restore(AuthUser $authUser, SalesOrderItem $salesOrderItem): bool
    {
        return $authUser->can('Restore:SalesOrderItem');
    }

    public function forceDelete(AuthUser $authUser, SalesOrderItem $salesOrderItem): bool
    {
        return $authUser->can('ForceDelete:SalesOrderItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SalesOrderItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SalesOrderItem');
    }

    public function replicate(AuthUser $authUser, SalesOrderItem $salesOrderItem): bool
    {
        return $authUser->can('Replicate:SalesOrderItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SalesOrderItem');
    }
}
