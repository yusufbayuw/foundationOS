<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\MarketplaceProductCategoryResource;

class ViewMarketplaceProductCategory extends ViewRecord
{
    protected static string $resource = MarketplaceProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
