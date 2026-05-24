<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\MarketplaceOrderItemResource;

class CreateMarketplaceOrderItem extends CreateRecord
{
    protected static string $resource = MarketplaceOrderItemResource::class;
}
