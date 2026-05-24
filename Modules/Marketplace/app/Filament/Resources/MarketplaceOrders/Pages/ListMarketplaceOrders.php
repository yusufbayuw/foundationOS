<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrders\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Marketplace\Filament\Resources\MarketplaceOrders\MarketplaceOrderResource;

class ListMarketplaceOrders extends ListRecords
{
    protected static string $resource = MarketplaceOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
