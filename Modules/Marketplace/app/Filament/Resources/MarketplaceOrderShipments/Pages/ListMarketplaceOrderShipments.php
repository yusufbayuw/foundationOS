<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments\MarketplaceOrderShipmentResource;

class ListMarketplaceOrderShipments extends ListRecords
{
    protected static string $resource = MarketplaceOrderShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
