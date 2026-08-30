<?php

declare(strict_types=1);

namespace Modules\Marketplace\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Marketplace\Models\MarketplaceProductReview;

class MarketplaceProductReviewPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MarketplaceProductReview');
    }

    public function view(AuthUser $authUser, MarketplaceProductReview $marketplaceProductReview): bool
    {
        return $authUser->can('View:MarketplaceProductReview');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MarketplaceProductReview');
    }

    public function update(AuthUser $authUser, MarketplaceProductReview $marketplaceProductReview): bool
    {
        return $authUser->can('Update:MarketplaceProductReview');
    }

    public function delete(AuthUser $authUser, MarketplaceProductReview $marketplaceProductReview): bool
    {
        return $authUser->can('Delete:MarketplaceProductReview');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MarketplaceProductReview');
    }

    public function restore(AuthUser $authUser, MarketplaceProductReview $marketplaceProductReview): bool
    {
        return $authUser->can('Restore:MarketplaceProductReview');
    }

    public function forceDelete(AuthUser $authUser, MarketplaceProductReview $marketplaceProductReview): bool
    {
        return $authUser->can('ForceDelete:MarketplaceProductReview');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MarketplaceProductReview');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MarketplaceProductReview');
    }

    public function replicate(AuthUser $authUser, MarketplaceProductReview $marketplaceProductReview): bool
    {
        return $authUser->can('Replicate:MarketplaceProductReview');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MarketplaceProductReview');
    }
}
