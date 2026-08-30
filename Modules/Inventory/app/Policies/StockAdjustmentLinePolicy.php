<?php

declare(strict_types=1);

namespace Modules\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Inventory\Models\StockAdjustmentLine;

class StockAdjustmentLinePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StockAdjustmentLine');
    }

    public function view(AuthUser $authUser, StockAdjustmentLine $stockAdjustmentLine): bool
    {
        return $authUser->can('View:StockAdjustmentLine');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StockAdjustmentLine');
    }

    public function update(AuthUser $authUser, StockAdjustmentLine $stockAdjustmentLine): bool
    {
        return $authUser->can('Update:StockAdjustmentLine');
    }

    public function delete(AuthUser $authUser, StockAdjustmentLine $stockAdjustmentLine): bool
    {
        return $authUser->can('Delete:StockAdjustmentLine');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StockAdjustmentLine');
    }

    public function restore(AuthUser $authUser, StockAdjustmentLine $stockAdjustmentLine): bool
    {
        return $authUser->can('Restore:StockAdjustmentLine');
    }

    public function forceDelete(AuthUser $authUser, StockAdjustmentLine $stockAdjustmentLine): bool
    {
        return $authUser->can('ForceDelete:StockAdjustmentLine');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StockAdjustmentLine');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StockAdjustmentLine');
    }

    public function replicate(AuthUser $authUser, StockAdjustmentLine $stockAdjustmentLine): bool
    {
        return $authUser->can('Replicate:StockAdjustmentLine');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StockAdjustmentLine');
    }
}
