<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\MarketplaceProductCategoryResource;

class ListMarketplaceProductCategories extends ListRecords
{
    protected static string $resource = MarketplaceProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
