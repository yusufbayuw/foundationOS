<?php

namespace Modules\Marketplace\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Marketplace\Models\MarketplaceProduct;

class MarketplaceProductPolicy
{
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MarketplaceProduct');
    }

    public function view(AuthUser $authUser, MarketplaceProduct $marketplaceProduct): bool
    {
        return $authUser->can('View:MarketplaceProduct');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MarketplaceProduct');
    }

    public function update(AuthUser $authUser, MarketplaceProduct $marketplaceProduct): bool
    {
        return $authUser->can('Update:MarketplaceProduct');
    }

    public function delete(AuthUser $authUser, MarketplaceProduct $marketplaceProduct): bool
    {
        return $authUser->can('Delete:MarketplaceProduct');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MarketplaceProduct');
    }

    public function restore(AuthUser $authUser, MarketplaceProduct $marketplaceProduct): bool
    {
        return $authUser->can('Restore:MarketplaceProduct');
    }

    public function forceDelete(AuthUser $authUser, MarketplaceProduct $marketplaceProduct): bool
    {
        return $authUser->can('ForceDelete:MarketplaceProduct');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MarketplaceProduct');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MarketplaceProduct');
    }

    public function replicate(AuthUser $authUser, MarketplaceProduct $marketplaceProduct): bool
    {
        return $authUser->can('Replicate:MarketplaceProduct');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MarketplaceProduct');
    }
}
