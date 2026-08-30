<?php

declare(strict_types=1);

namespace Modules\Marketplace\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Marketplace\Models\MarketplaceProductCategory;

class MarketplaceProductCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MarketplaceProductCategory');
    }

    public function view(AuthUser $authUser, MarketplaceProductCategory $marketplaceProductCategory): bool
    {
        return $authUser->can('View:MarketplaceProductCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MarketplaceProductCategory');
    }

    public function update(AuthUser $authUser, MarketplaceProductCategory $marketplaceProductCategory): bool
    {
        return $authUser->can('Update:MarketplaceProductCategory');
    }

    public function delete(AuthUser $authUser, MarketplaceProductCategory $marketplaceProductCategory): bool
    {
        return $authUser->can('Delete:MarketplaceProductCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MarketplaceProductCategory');
    }

    public function restore(AuthUser $authUser, MarketplaceProductCategory $marketplaceProductCategory): bool
    {
        return $authUser->can('Restore:MarketplaceProductCategory');
    }

    public function forceDelete(AuthUser $authUser, MarketplaceProductCategory $marketplaceProductCategory): bool
    {
        return $authUser->can('ForceDelete:MarketplaceProductCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MarketplaceProductCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MarketplaceProductCategory');
    }

    public function replicate(AuthUser $authUser, MarketplaceProductCategory $marketplaceProductCategory): bool
    {
        return $authUser->can('Replicate:MarketplaceProductCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MarketplaceProductCategory');
    }
}
