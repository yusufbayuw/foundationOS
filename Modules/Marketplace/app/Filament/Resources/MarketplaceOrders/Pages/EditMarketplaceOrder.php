<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrders\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceOrders\MarketplaceOrderResource;

class EditMarketplaceOrder extends EditRecord
{
    protected static string $resource = MarketplaceOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
