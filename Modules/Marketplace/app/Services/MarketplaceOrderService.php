<?php

namespace Modules\Marketplace\Services;

use Illuminate\Database\Eloquent\Builder;
use Modules\Marketplace\Models\MarketplaceOrder;
use Modules\Marketplace\Models\Seller;

class MarketplaceOrderService
{
    /** @return Builder<MarketplaceOrder> */
    public function ordersForSeller(Seller $seller): Builder
    {
        return MarketplaceOrder::query()
            ->where('tenant_id', $seller->tenant_id)
            ->where('seller_id', $seller->getKey());
    }

    public function assertSellerIsolation(Seller $seller, MarketplaceOrder $order): void
    {
        if ((int) $order->seller_id !== (int) $seller->getKey()) {
            throw new \RuntimeException('Order does not belong to this seller.');
        }
    }
}
