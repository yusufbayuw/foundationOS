<?php

declare(strict_types=1);

namespace Modules\Marketplace\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Marketplace\Models\MarketplaceOrderItem;

class MarketplaceOrderItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MarketplaceOrderItem');
    }

    public function view(AuthUser $authUser, MarketplaceOrderItem $marketplaceOrderItem): bool
    {
        return $authUser->can('View:MarketplaceOrderItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MarketplaceOrderItem');
    }

    public function update(AuthUser $authUser, MarketplaceOrderItem $marketplaceOrderItem): bool
    {
        return $authUser->can('Update:MarketplaceOrderItem');
    }

    public function delete(AuthUser $authUser, MarketplaceOrderItem $marketplaceOrderItem): bool
    {
        return $authUser->can('Delete:MarketplaceOrderItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MarketplaceOrderItem');
    }

    public function restore(AuthUser $authUser, MarketplaceOrderItem $marketplaceOrderItem): bool
    {
        return $authUser->can('Restore:MarketplaceOrderItem');
    }

    public function forceDelete(AuthUser $authUser, MarketplaceOrderItem $marketplaceOrderItem): bool
    {
        return $authUser->can('ForceDelete:MarketplaceOrderItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MarketplaceOrderItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MarketplaceOrderItem');
    }

    public function replicate(AuthUser $authUser, MarketplaceOrderItem $marketplaceOrderItem): bool
    {
        return $authUser->can('Replicate:MarketplaceOrderItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MarketplaceOrderItem');
    }
}
