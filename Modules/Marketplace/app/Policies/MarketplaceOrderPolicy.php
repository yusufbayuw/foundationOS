<?php

declare(strict_types=1);

namespace Modules\Marketplace\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Marketplace\Models\MarketplaceOrder;

class MarketplaceOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MarketplaceOrder');
    }

    public function view(AuthUser $authUser, MarketplaceOrder $marketplaceOrder): bool
    {
        return $authUser->can('View:MarketplaceOrder');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MarketplaceOrder');
    }

    public function update(AuthUser $authUser, MarketplaceOrder $marketplaceOrder): bool
    {
        return $authUser->can('Update:MarketplaceOrder');
    }

    public function delete(AuthUser $authUser, MarketplaceOrder $marketplaceOrder): bool
    {
        return $authUser->can('Delete:MarketplaceOrder');
    }

    public function export(AuthUser $authUser): bool
    {
        return $authUser->can('Export:MarketplaceOrder');
    }
}
