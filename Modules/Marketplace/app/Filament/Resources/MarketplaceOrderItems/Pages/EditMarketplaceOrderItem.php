<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceOrderItems\MarketplaceOrderItemResource;

class EditMarketplaceOrderItem extends EditRecord
{
    protected static string $resource = MarketplaceOrderItemResource::class;

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
