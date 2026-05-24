<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductReviews\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceProductReviews\MarketplaceProductReviewResource;

class ViewMarketplaceProductReview extends ViewRecord
{
    protected static string $resource = MarketplaceProductReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
