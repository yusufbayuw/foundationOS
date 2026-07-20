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
}
