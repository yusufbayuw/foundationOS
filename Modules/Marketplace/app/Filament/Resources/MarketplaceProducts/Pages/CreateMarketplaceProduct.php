<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProducts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceProducts\MarketplaceProductResource;

class CreateMarketplaceProduct extends CreateRecord
{
    protected static string $resource = MarketplaceProductResource::class;
}
