<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\MarketplaceProductVariantResource;

class ViewMarketplaceProductVariant extends ViewRecord
{
    protected static string $resource = MarketplaceProductVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
