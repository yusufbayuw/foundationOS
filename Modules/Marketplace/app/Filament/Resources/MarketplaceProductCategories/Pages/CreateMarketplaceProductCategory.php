<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\MarketplaceProductCategoryResource;

class CreateMarketplaceProductCategory extends CreateRecord
{
    protected static string $resource = MarketplaceProductCategoryResource::class;
}
