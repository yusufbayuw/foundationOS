<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\MarketplaceProductCategoryResource;

class EditMarketplaceProductCategory extends EditRecord
{
    protected static string $resource = MarketplaceProductCategoryResource::class;

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
