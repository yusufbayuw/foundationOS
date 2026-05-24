<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\MarketplaceProductVariantResource;

class ListMarketplaceProductVariants extends ListRecords
{
    protected static string $resource = MarketplaceProductVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
