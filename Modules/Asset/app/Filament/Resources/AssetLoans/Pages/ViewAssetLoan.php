<?php

namespace Modules\Asset\Filament\Resources\AssetLoans\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Asset\Filament\Resources\AssetLoans\AssetLoanResource;

class ViewAssetLoan extends ViewRecord
{
    protected static string $resource = AssetLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
