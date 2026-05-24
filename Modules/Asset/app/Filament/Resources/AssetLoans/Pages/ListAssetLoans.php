<?php

namespace Modules\Asset\Filament\Resources\AssetLoans\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Asset\Filament\Resources\AssetLoans\AssetLoanResource;

class ListAssetLoans extends ListRecords
{
    protected static string $resource = AssetLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
