<?php

declare(strict_types=1);

namespace Modules\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Inventory\Models\StockCostLayer;

class StockCostLayerPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StockCostLayer');
    }

    public function view(AuthUser $authUser, StockCostLayer $stockCostLayer): bool
    {
        return $authUser->can('View:StockCostLayer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StockCostLayer');
    }

    public function update(AuthUser $authUser, StockCostLayer $stockCostLayer): bool
    {
        return $authUser->can('Update:StockCostLayer');
    }

    public function delete(AuthUser $authUser, StockCostLayer $stockCostLayer): bool
    {
        return $authUser->can('Delete:StockCostLayer');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StockCostLayer');
    }

    public function restore(AuthUser $authUser, StockCostLayer $stockCostLayer): bool
    {
        return $authUser->can('Restore:StockCostLayer');
    }

    public function forceDelete(AuthUser $authUser, StockCostLayer $stockCostLayer): bool
    {
        return $authUser->can('ForceDelete:StockCostLayer');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StockCostLayer');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StockCostLayer');
    }

    public function replicate(AuthUser $authUser, StockCostLayer $stockCostLayer): bool
    {
        return $authUser->can('Replicate:StockCostLayer');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StockCostLayer');
    }
}
