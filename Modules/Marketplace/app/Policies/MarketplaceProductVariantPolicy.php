<?php

declare(strict_types=1);

namespace Modules\Marketplace\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Marketplace\Models\MarketplaceProductVariant;

class MarketplaceProductVariantPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MarketplaceProductVariant');
    }

    public function view(AuthUser $authUser, MarketplaceProductVariant $marketplaceProductVariant): bool
    {
        return $authUser->can('View:MarketplaceProductVariant');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MarketplaceProductVariant');
    }

    public function update(AuthUser $authUser, MarketplaceProductVariant $marketplaceProductVariant): bool
    {
        return $authUser->can('Update:MarketplaceProductVariant');
    }

    public function delete(AuthUser $authUser, MarketplaceProductVariant $marketplaceProductVariant): bool
    {
        return $authUser->can('Delete:MarketplaceProductVariant');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MarketplaceProductVariant');
    }

    public function restore(AuthUser $authUser, MarketplaceProductVariant $marketplaceProductVariant): bool
    {
        return $authUser->can('Restore:MarketplaceProductVariant');
    }

    public function forceDelete(AuthUser $authUser, MarketplaceProductVariant $marketplaceProductVariant): bool
    {
        return $authUser->can('ForceDelete:MarketplaceProductVariant');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MarketplaceProductVariant');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MarketplaceProductVariant');
    }

    public function replicate(AuthUser $authUser, MarketplaceProductVariant $marketplaceProductVariant): bool
    {
        return $authUser->can('Replicate:MarketplaceProductVariant');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MarketplaceProductVariant');
    }
}
