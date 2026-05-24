<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\MarketplaceOrderItemResource;

class ListMarketplaceOrderItems extends ListRecords
{
    protected static string $resource = MarketplaceOrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
