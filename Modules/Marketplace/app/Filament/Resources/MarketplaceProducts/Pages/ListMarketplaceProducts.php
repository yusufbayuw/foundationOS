<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProducts\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Marketplace\Filament\Resources\MarketplaceProducts\MarketplaceProductResource;

class ListMarketplaceProducts extends ListRecords
{
    protected static string $resource = MarketplaceProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
