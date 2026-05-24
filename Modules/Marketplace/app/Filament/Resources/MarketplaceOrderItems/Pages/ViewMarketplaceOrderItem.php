<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\MarketplaceOrderItemResource;

class ViewMarketplaceOrderItem extends ViewRecord
{
    protected static string $resource = MarketplaceOrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
