<?php

declare(strict_types=1);

namespace Modules\Marketplace\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Marketplace\Models\MarketplaceOrderShipment;

class MarketplaceOrderShipmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MarketplaceOrderShipment');
    }

    public function view(AuthUser $authUser, MarketplaceOrderShipment $marketplaceOrderShipment): bool
    {
        return $authUser->can('View:MarketplaceOrderShipment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MarketplaceOrderShipment');
    }

    public function update(AuthUser $authUser, MarketplaceOrderShipment $marketplaceOrderShipment): bool
    {
        return $authUser->can('Update:MarketplaceOrderShipment');
    }

    public function delete(AuthUser $authUser, MarketplaceOrderShipment $marketplaceOrderShipment): bool
    {
        return $authUser->can('Delete:MarketplaceOrderShipment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MarketplaceOrderShipment');
    }

    public function restore(AuthUser $authUser, MarketplaceOrderShipment $marketplaceOrderShipment): bool
    {
        return $authUser->can('Restore:MarketplaceOrderShipment');
    }

    public function forceDelete(AuthUser $authUser, MarketplaceOrderShipment $marketplaceOrderShipment): bool
    {
        return $authUser->can('ForceDelete:MarketplaceOrderShipment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MarketplaceOrderShipment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MarketplaceOrderShipment');
    }

    public function replicate(AuthUser $authUser, MarketplaceOrderShipment $marketplaceOrderShipment): bool
    {
        return $authUser->can('Replicate:MarketplaceOrderShipment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MarketplaceOrderShipment');
    }
}
