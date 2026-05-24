<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\MarketplaceProductVariantResource;

class CreateMarketplaceProductVariant extends CreateRecord
{
    protected static string $resource = MarketplaceProductVariantResource::class;
}
