<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductReviews\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceProductReviews\MarketplaceProductReviewResource;

class EditMarketplaceProductReview extends EditRecord
{
    protected static string $resource = MarketplaceProductReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
