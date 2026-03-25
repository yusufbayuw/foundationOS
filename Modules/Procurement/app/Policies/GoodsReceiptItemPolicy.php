<?php

declare(strict_types=1);

namespace Modules\Procurement\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Procurement\Models\GoodsReceiptItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoodsReceiptItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GoodsReceiptItem');
    }

    public function view(AuthUser $authUser, GoodsReceiptItem $goodsReceiptItem): bool
    {
        return $authUser->can('View:GoodsReceiptItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GoodsReceiptItem');
    }

    public function update(AuthUser $authUser, GoodsReceiptItem $goodsReceiptItem): bool
    {
        return $authUser->can('Update:GoodsReceiptItem');
    }

    public function delete(AuthUser $authUser, GoodsReceiptItem $goodsReceiptItem): bool
    {
        return $authUser->can('Delete:GoodsReceiptItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:GoodsReceiptItem');
    }

    public function restore(AuthUser $authUser, GoodsReceiptItem $goodsReceiptItem): bool
    {
        return $authUser->can('Restore:GoodsReceiptItem');
    }

    public function forceDelete(AuthUser $authUser, GoodsReceiptItem $goodsReceiptItem): bool
    {
        return $authUser->can('ForceDelete:GoodsReceiptItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GoodsReceiptItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GoodsReceiptItem');
    }

    public function replicate(AuthUser $authUser, GoodsReceiptItem $goodsReceiptItem): bool
    {
        return $authUser->can('Replicate:GoodsReceiptItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GoodsReceiptItem');
    }

}