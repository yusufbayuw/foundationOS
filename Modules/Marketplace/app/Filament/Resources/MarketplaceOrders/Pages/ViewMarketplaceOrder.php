<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrders\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceOrders\MarketplaceOrderResource;

class ViewMarketplaceOrder extends ViewRecord
{
    protected static string $resource = MarketplaceOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
