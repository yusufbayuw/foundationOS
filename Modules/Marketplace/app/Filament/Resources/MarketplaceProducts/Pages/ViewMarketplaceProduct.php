<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProducts\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceProducts\MarketplaceProductResource;

class ViewMarketplaceProduct extends ViewRecord
{
    protected static string $resource = MarketplaceProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
