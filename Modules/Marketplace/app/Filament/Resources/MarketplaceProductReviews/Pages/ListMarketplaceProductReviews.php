<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductReviews\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Marketplace\Filament\Resources\MarketplaceProductReviews\MarketplaceProductReviewResource;

class ListMarketplaceProductReviews extends ListRecords
{
    protected static string $resource = MarketplaceProductReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
