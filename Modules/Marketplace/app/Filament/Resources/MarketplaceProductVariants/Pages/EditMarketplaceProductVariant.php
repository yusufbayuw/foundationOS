<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\MarketplaceProductVariantResource;

class EditMarketplaceProductVariant extends EditRecord
{
    protected static string $resource = MarketplaceProductVariantResource::class;

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
