<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderShipments\MarketplaceOrderShipmentResource;

class EditMarketplaceOrderShipment extends EditRecord
{
    protected static string $resource = MarketplaceOrderShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
