<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrders\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceOrders\MarketplaceOrderResource;

class CreateMarketplaceOrder extends CreateRecord
{
    protected static string $resource = MarketplaceOrderResource::class;
}
