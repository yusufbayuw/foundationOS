<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments\MarketplaceOrderShipmentResource;

class ViewMarketplaceOrderShipment extends ViewRecord
{
    protected static string $resource = MarketplaceOrderShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
